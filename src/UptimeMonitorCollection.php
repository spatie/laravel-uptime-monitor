<?php

namespace Spatie\UptimeMonitor\Services\PingMonitors;

use Cache;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Collection;
use Log;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Models\UptimeMonitor;

class UptimeMonitorCollection extends Collection
{
    public function check()
    {
        $this->resetItemKeys();



        (new EachPromise($this->getPromises(), [
            'concurrency' => 100,
            'fulfilled' => function (ResponseInterface $response, $index) {
                $uptimeMonitor = $this->items[$index];

                consoleOutput()->info("Could reach {$uptimeMonitor->url}");

                $uptimeMonitor->pingSucceeded($response->getBody());
            },

            'rejected' => function (RequestException $exception, $index) {
                $uptimeMonitor = $this->items[$index];

                consoleOutput()->error("Could not reach {$uptimeMonitor->url} error: `{$exception->getMessage()}`");

                $uptimeMonitor->pingFailed($exception->getMessage());
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

        foreach ($this->items as $uptimeMonitor) {
            consoleOutput()->info("checking {$uptimeMonitor->url}");

            $promise = $client->requestAsync(
                $uptimeMonitor->getPingRequestMethod(),
                $uptimeMonitor->url,
                ['connect_timeout' => 10]
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
