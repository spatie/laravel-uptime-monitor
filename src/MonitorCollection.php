<?php

namespace Spatie\UptimeMonitor;

use Generator;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;

class MonitorCollection extends Collection
{
    public function checkUptime(): void
    {
        $this->resetItemKeys();

        (new EachPromise($this->getPromises(), [
            'concurrency' => config('uptime-monitor.uptime_check.concurrent_checks'),
            'fulfilled' => function (ResponseInterface $response, $index) {
                $monitor = $this->getMonitorAtIndex($index);

                ConsoleOutput::info("Could reach {$monitor->url}");

                $monitor->uptimeRequestSucceeded($response);
            },

            'rejected' => function (RequestException $exception, $index) {
                $monitor = $this->getMonitorAtIndex($index);

                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");

                $monitor->uptimeRequestFailed($exception->getMessage());
            },
        ]))->promise()->wait();
    }

    protected function getPromises(): Generator
    {
        $client = GuzzleFactory::make(
            config('uptime-monitor.uptime_check.guzzle_options', []),
            config('uptime-monitor.uptime-check.retry_connection_after_milliseconds', 100)
        );

        foreach ($this->items as $monitor) {
            ConsoleOutput::info("Checking {$monitor->url}");

            $promise = $client->requestAsync(
                $monitor->uptime_check_method,
                $monitor->url,
                array_filter([
                    'connect_timeout' => config('uptime-monitor.uptime_check.timeout_per_site'),
                    'headers' => $this->promiseHeaders($monitor),
                    'body' => $monitor->uptime_check_payload,
                ])
            );

            yield $promise;
        }
    }

    private function promiseHeaders(Monitor $monitor): array
    {
        return collect([])
            ->merge(['User-Agent' => config('uptime-monitor.uptime_check.user_agent')])
            ->merge(config('uptime-monitor.uptime_check.additional_headers') ?? [])
            ->merge($monitor->uptime_check_additional_headers)
            ->toArray();
    }

    /**
     * In order to make use of Guzzle promises we have to make sure the
     * keys of the collection are in a consecutive order without gaps.
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
        return $this->sortBy(function (Monitor $monitor) {
            return $monitor->url->getHost();
        });
    }
}
