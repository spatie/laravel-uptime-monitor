<?php

namespace Spatie\UptimeMonitor\Services\PingMonitors;

use Spatie\UptimeMonitor\Models\PingMonitor;
use Cache;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Collection;
use Log;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Models\UptimeMonitor;

class UptimeMonitorCollection extends Collection
{
    public function check()
    {
        $this->resetItemKeys();

        Log::info("Start checking for {$this->count()} monitors...");

        (new EachPromise($this->getPromises(), [
            'concurrency' => 100,
            'fulfilled' => function (ResponseInterface $response, $index) {
                $pingMonitor = $this->items[$index];

                $this->log('fulfilled ping', $pingMonitor);

                $pingMonitor->pingSucceeded($response->getBody());

                $this->cacheResponse($response, $pingMonitor);
            },

            'rejected' => function (RequestException $exception, $index) {
                $pingMonitor = $this->items[$index];

                $this->log("rejected ping because: {$exception->getMessage()}", $pingMonitor);

                $pingMonitor->pingFailed($exception->getMessage());

                $this->cacheException($exception, $pingMonitor);
            },
        ]))->promise()->wait();

        Log::info('Checking done!');
    }

    protected function getPromises() : Generator
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'spatie/laravel-uptime-monitor uptime checker',
            ],
        ]);

        foreach ($this->items as $pingMonitor) {
            $this->log('checking', $pingMonitor);

            $promise = $this->getCachedResponse($pingMonitor);

            if (!$promise instanceof PromiseInterface) {
                $this->log('use cached response', $pingMonitor);

                $promise = $client->requestAsync(
                    $pingMonitor->getPingRequestMethod(),
                    $pingMonitor->url,
                    ['connect_timeout' => 10]
                );
            }

            yield $promise;
        }
    }

    /**
     * @param \Spatie\UptimeMonitor\Models\UptimeMonitor $uptimeMonitor
     *
     * @return bool|PromiseInterface
     */
    protected function getCachedResponse(UptimeMonitor $uptimeMonitor)
    {
        $cachedResult = Cache::get($uptimeMonitor->getCacheKey());

        if ($cachedResult instanceof ResponseInterface) {
            return new FulfilledPromise($cachedResult);
        }

        if (is_string($cachedResult)) {
            return new RejectedPromise(
                new RequestException($cachedResult, new Request($uptimeMonitor->getPingRequestMethod(), $uptimeMonitor->url))
            );
        }

        return false;
    }

    protected function cacheResponse(ResponseInterface $response, PingMonitor $pingMonitor)
    {
        Cache::put($pingMonitor->getCacheKey(), $response, 1);
    }

    protected function cacheException(RequestException $exception, PingMonitor $pingMonitor)
    {
        Cache::put($pingMonitor->getCacheKey(), $exception->getMessage(), 1);
    }

    /**
     * Make sure the keys are in consecutive order without gaps.
     */
    protected function resetItemKeys()
    {
        $this->items = $this->values()->all();
    }

    public function log($message, PingMonitor $pingMonitor)
    {
        Log::info("$message (url: {$pingMonitor->url} id: {$pingMonitor->id})");
    }
}
