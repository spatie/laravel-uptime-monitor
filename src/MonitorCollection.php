<?php

namespace Spatie\UptimeMonitor;

use Generator;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use GuzzleHttp\Promise\EachPromise;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Models\Monitor;
use GuzzleHttp\Exception\RequestException;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class MonitorCollection extends Collection
{
    public function checkUptime()
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
        // client headers
        $headers = array_merge(
            ['User-Agent' => config('uptime-monitor.uptime_check.user_agent')],
            config('uptime-monitor.uptime_check.additional_headers') ?? []
        );

        $client = GuzzleFactory::make(
            compact('headers'),
            config('uptime-monitor.uptime-check.retry_connection_after_milliseconds', 100)
        );

        foreach ($this->items as $monitor) {
            ConsoleOutput::info("Checking {$monitor->url}");
            $promise = $client->requestAsync(
                $monitor->uptime_check_method,
                $monitor->url,
                [
                    'connect_timeout' => config('uptime-monitor.uptime_check.timeout_per_site'),
                    'headers' => $headers,
                ]
            );

            yield $promise;
        }
    }

    /**
     * In order to make use of Guzzle promises we have to make sure the
     * keys of the collection are in a consecutive order without gaps.
     */
    protected function resetItemKeys()
    {
        $this->items = $this->values()->all();
    }

    protected function getMonitorAtIndex(int $index): Monitor
    {
        return $this->items[$index];
    }

    /**
     * @return static
     */
    public function sortByHost()
    {
        return $this->sortBy(function (Monitor $monitor) {
            return $monitor->url->getHost();
        });
    }
}
