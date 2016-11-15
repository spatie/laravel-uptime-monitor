<?php

namespace Spatie\UptimeMonitor;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class SiteCollection extends Collection
{
    public function checkUptime()
    {
        $this->resetItemKeys();

        (new EachPromise($this->getPromises(), [
            'concurrency' => config('laravel-uptime-monitor.uptime_check.concurrent_checks'),
            'fulfilled' => function (ResponseInterface $response, $index) {
                $site = $this->items[$index];

                uptimeMonitorConsoleOutput()->info("Could reach {$site->url}");

                $site->pingSucceeded($response->getBody());
            },

            'rejected' => function (RequestException $exception, $index) {
                $site = $this->items[$index];

                uptimeMonitorConsoleOutput()->error("Could not reach {$site->url} error: `{$exception->getMessage()}`");

                $site->pingFailed($exception->getMessage());
            },
        ]))->promise()->wait();
    }

    protected function getPromises() : Generator
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'spatie/laravel-uptime-monitor uptime checker',
            ],
        ]);

        foreach ($this->items as $site) {
            uptimeMonitorConsoleOutput()->info("checking {$site->url}");
            $promise = $client->requestAsync(
                $site->uptime_check_method,
                $site->url,
                ['connect_timeout' => config('laravel-uptime-monitor.uptime_check.timeout_per_site')]
            );

            yield $promise;
        }
    }

    /**
     * Make sure the keys are in consecutive order without gaps.
     */
    protected function resetItemKeys()
    {
        $this->items = $this->values()->all();
    }
}
