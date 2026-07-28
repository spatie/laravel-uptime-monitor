<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Throwable;

class MonitorCollection extends Collection
{
    /**
     * The maximum duration of a single request of an uptime check, in seconds.
     * It can be overridden through the configured client options.
     */
    protected const TRANSPORT_TIMEOUT = 15;

    /**
     * The maximum number of attempts made per uptime check.
     */
    protected const MAX_ATTEMPTS = 4;

    /**
     * Response status codes that are retried in addition to server errors.
     */
    protected const RETRYABLE_STATUS_CODES = [429];

    /**
     * The number of characters of the response body kept in a failure reason.
     */
    protected const FAILURE_REASON_BODY_LENGTH = 250;

    public function checkUptime(): void
    {
        $this->resetItemKeys();

        $results = Http::pool(
            $this->addRequestsToPool(...),
            config('uptime-monitor.uptime_check.concurrent_checks'),
        );

        foreach ($results as $index => $result) {
            $this->handleResult($this->getMonitorAtIndex($index), $result);
        }
    }

    protected function addRequestsToPool(Pool $pool): void
    {
        foreach ($this->items as $monitor) {
            ConsoleOutput::info("Checking {$monitor->url}");

            $this->pendingRequest($pool, $monitor)->send(
                strtoupper($monitor->uptime_check_method),
                (string) $monitor->url,
            );
        }
    }

    protected function pendingRequest(Pool $pool, Monitor $monitor): PendingRequest
    {
        $headers = $this->requestHeaders($monitor);
        $contentType = array_change_key_case($headers)['content-type'] ?? null;

        $request = $pool->newRequest()
            ->timeout(static::TRANSPORT_TIMEOUT)
            ->truncateExceptionsAt(static::FAILURE_REASON_BODY_LENGTH)
            ->retry(
                static::MAX_ATTEMPTS,
                fn (int $attempt) => (2 ** $attempt) * config('uptime-monitor.uptime_check.retry_connection_after_milliseconds', 100),
                $this->shouldRetry(...),
                false,
            )
            // The configured options are applied first so that the headers and timeouts
            // belonging to the monitor itself continue to take precedence over them.
            ->withOptions(config('uptime-monitor.uptime_check.guzzle_options', []))
            ->replaceHeaders($headers)
            ->connectTimeout(config('uptime-monitor.uptime_check.timeout_per_site'))
            ->when(
                filled($monitor->uptime_check_payload),
                fn (PendingRequest $request): PendingRequest => $request->withBody($monitor->uptime_check_payload, $contentType ?? 'application/json'),
            );

        // The HTTP client sends a JSON content type by default. An uptime check should only
        // announce a content type when one was explicitly configured for the monitor.
        return $request->beforeSending(function (Request $request) use ($contentType) {
            $psrRequest = $request->toPsrRequest()->withoutHeader('Content-Type');

            return $contentType === null
                ? $psrRequest
                : $psrRequest->withHeader('Content-Type', $contentType);
        });
    }

    protected function handleResult(Monitor $monitor, Response|Throwable $result): void
    {
        if ($result instanceof Response && $this->isSuccessfulResponse($result)) {
            ConsoleOutput::info("Could reach {$monitor->url}");

            $monitor->uptimeRequestSucceeded($result->toPsrResponse());

            return;
        }

        $reason = $this->failureReason($result);

        ConsoleOutput::error("Could not reach {$monitor->url} error: `{$reason}`");

        $monitor->uptimeRequestFailed($reason);
    }

    protected function isSuccessfulResponse(Response $response): bool
    {
        if (! $response->clientError() && ! $response->serverError()) {
            return true;
        }

        return in_array($response->status(), config('uptime-monitor.uptime_check.additional_status_codes', []));
    }

    protected function failureReason(Response|Throwable $result): string
    {
        if (! $result instanceof Response) {
            return $result->getMessage();
        }

        return $result->toException()?->getMessage()
            ?? "HTTP request returned status code {$result->status()}";
    }

    protected function shouldRetry(?Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        if (! $exception instanceof RequestException) {
            return false;
        }

        return $exception->response->serverError()
            || in_array($exception->response->status(), static::RETRYABLE_STATUS_CODES);
    }

    private function requestHeaders(Monitor $monitor): array
    {
        return collect([])
            ->merge(['User-Agent' => config('uptime-monitor.uptime_check.user_agent')])
            ->merge(config('uptime-monitor.uptime_check.additional_headers') ?? [])
            ->merge($monitor->uptime_check_additional_headers)
            ->toArray();
    }

    /**
     * In order to match the monitors up with the responses of the request pool we have
     * to make sure the keys of the collection are in a consecutive order without gaps.
     */
    protected function resetItemKeys(): void
    {
        $this->items = $this->values()->all();
    }

    protected function getMonitorAtIndex(int $index): Monitor
    {
        return $this->items[$index];
    }

    public function sortByHost(): self
    {
        return $this->sortBy(fn (Monitor $monitor): string => $monitor->url->getHost());
    }
}
