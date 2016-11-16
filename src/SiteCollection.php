<?php

namespace Spatie\UptimeMonitor;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Site;

class SiteCollection extends Collection
{
    public function checkUptime()
    {
        $this->resetItemKeys();

        (new EachPromise($this->getPromises(), [
            'concurrency' => config('laravel-uptime-monitor.uptime_check.concurrent_checks'),
            'fulfilled' => function (ResponseInterface $response, $index) {
                $site = $this->getSiteAtIndex($index);

                ConsoleOutput::info("Could reach {$site->url}");

                $site->couldReachSite($response->getBody());
            },

            'rejected' => function (RequestException $exception, $index) {
                $site = $this->getSiteAtIndex($index);

                ConsoleOutput::error("Could not reach {$site->url} error: `{$exception->getMessage()}`");

                $site->couldNotReachSite($exception->getMessage());
            },
        ]))->promise()->wait();
    }

    protected function getPromises() : Generator
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => config('laravel-uptime-monitor.uptime_check.user_agent'),
            ],
        ]);

        foreach ($this->items as $site) {
            ConsoleOutput::info("checking {$site->url}");
            $promise = $client->requestAsync(
                $site->uptime_check_method,
                $site->url,
                ['connect_timeout' => config('laravel-uptime-monitor.uptime_check.timeout_per_site')]
            );

            yield $promise;
        }
    }

    /**
     * In order to make use of Guzzle promises we have to make sure the
     * keys of the collection are in consecutive order without gaps.
     */
    protected function resetItemKeys()
    {
        $this->items = $this->values()->all();
    }

    protected function getSiteAtIndex(int $index): Site
    {
        return $this->items[$index];
    }
}
