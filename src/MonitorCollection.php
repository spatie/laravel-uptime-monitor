<?php

namespace Spatie\UptimeMonitor;

use Generator;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Actions\SendUptimeCheckRequestAction;
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

            'rejected' => function (TransferException $exception, $index) {
                $monitor = $this->getMonitorAtIndex($index);

                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");

                $monitor->uptimeRequestFailed($exception->getMessage());
            },
        ]))->promise()->wait();
    }

    protected function getPromises(): Generator
    {
        $sendUptimeCheckRequest = $this->getSendUptimeCheckRequestAction();

        foreach ($this->items as $monitor) {
            ConsoleOutput::info("Checking {$monitor->url}");

            $promise = $sendUptimeCheckRequest->execute($monitor)->then(
                function (ResponseInterface $response) {
                    return $response;
                },
                function (TransferException $exception) {
                    if (in_array($exception->getCode(), config('uptime-monitor.uptime_check.additional_status_codes', []))) {
                        return $exception->getResponse();
                    }

                    throw $exception;
                }
            );

            yield $promise;
        }
    }

    protected function getSendUptimeCheckRequestAction(): SendUptimeCheckRequestAction
    {
        $actionClass = config(
            'uptime-monitor.actions.send_uptime_check_request',
            SendUptimeCheckRequestAction::class
        );

        return app($actionClass);
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
