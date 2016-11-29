<?php
namespace Spatie\UptimeMonitor\Checker;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\MonitorCollection;

class HTTPChecker extends Checker
{
    public function check(MonitorCollection $monitors)
    {
        $monitors->resetItemKeys();
        $this->monitors = $monitors;
        (new EachPromise($this->getPromises($monitors), [
            'concurrency' => config('laravel-uptime-monitor.uptime_check.concurrent_checks'),
            'fulfilled' => function (ResponseInterface $response, $index) {
                $monitor = $this->monitors->getMonitorAtIndex($index);

                ConsoleOutput::info("Could reach {$monitor->url}");

                $monitor->uptimeRequestSucceeded($response);
            },
            'rejected' => function (RequestException $exception, $index) {
                $monitor = $this->monitors->getMonitorAtIndex($index);
                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");
                $monitor->uptimeRequestFailed($exception->getMessage());
            },
        ]))->promise()->wait();
    }

    protected function getPromises(MonitorCollection $monitors): \Generator
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => config('laravel-uptime-monitor.uptime_check.user_agent'),
            ],
        ]);

        foreach ($monitors as $monitor) {
            ConsoleOutput::info("checking {$monitor->url}");
            $promise = $client->requestAsync(
                $monitor->uptime_check_method,
                $monitor->url,
                ['connect_timeout' => config('laravel-uptime-monitor.uptime_check.timeout_per_site')]
            );

            yield $promise;
        }
    }


}