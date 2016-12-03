<?php

namespace Spatie\UptimeMonitor;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;

class MonitorCollection extends Collection
{
    /**
     * @return static
     */
    public function sortByHost()
    {
        return $this->sortBy(function (Monitor $monitor) {
            return $monitor->url->getHost();
        });
    }

    public function checkUptime()
    {
        $this->resetItemKeys();

        (new EachPromise($this->getPromises(), [
            'concurrency' => config('laravel-uptime-monitor.uptime_check.concurrent_checks'),
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
            ['User-Agent' => config('laravel-uptime-monitor.uptime_check.user_agent')],
            config('laravel-uptime-monitor.uptime_check.additional_headers') ?? []
        );

        $client = new Client([
            'headers' => $headers,
        ]);

        foreach ($this->items as $monitor) {
            ConsoleOutput::info("Checking {$monitor->url}");
            $promise = $client->requestAsync(
                $monitor->uptime_check_method,
                $monitor->url,
                ['connect_timeout' => config('laravel-uptime-monitor.uptime_check.timeout_per_site')]
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
}
