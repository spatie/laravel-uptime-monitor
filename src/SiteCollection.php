<?php

namespace Spatie\UptimeMonitor\Services\PingMonitors;

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
            'concurrency' => 100,
            'fulfilled' => function (ResponseInterface $response, $index) {
                $site = $this->items[$index];

                consoleOutput()->info("Could reach {$site->url}");

                $site->pingSucceeded($response->getBody());
            },

            'rejected' => function (RequestException $exception, $index) {
                $site = $this->items[$index];

                consoleOutput()->error("Could not reach {$site->url} error: `{$exception->getMessage()}`");

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
            consoleOutput()->info("checking {$site->url}");

            $promise = $client->requestAsync(
                $site->getPingRequestMethod(),
                $site->url,
                ['connect_timeout' => 10]
            );

            //    ['connect_timeout' => 10, 'curl' => [CURLOPT_SSLVERSION =>CURL_SSLVERSION_SSLv3]]

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
