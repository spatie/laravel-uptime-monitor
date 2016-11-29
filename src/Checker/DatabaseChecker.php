<?php

namespace Spatie\UptimeMonitor\Checker;

use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\MonitorCollection;

class DatabaseChecker extends Checker
{
    /**
     * {@inheritdoc}
     */
    public function check(MonitorCollection $monitors)
    {
        $monitors->resetItemKeys();
        $this->monitors = $monitors;
        (new EachPromise($this->getPromises($monitors), [
            'concurrency' => config('laravel-uptime-monitor.uptime_check.concurrent_checks'),
            'fulfilled' => function ($monitor, $index) {
                $monitor = $this->monitors->getMonitorAtIndex($index);
                ConsoleOutput::info("Could reach {$monitor->url}");
            },
            'rejected' => function ($exception, $index) {
                $monitor = $this->monitors->getMonitorAtIndex($index);
                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");
            },
        ]))->promise()->wait();
    }

    protected function getPromises($monitors): \Generator
    {
        foreach ($monitors as $monitor) {
            $promise = with(new Promise())->then(null, function () use (&$promise, $monitor) {
                $urlSegments = explode('://', $monitor->url);
                $protocol = $urlSegments[0];
                $hostSegments = explode(':', $urlSegments[1]);
                $host = $hostSegments[0];
                $port = (array_key_exists(1, $hostSegments)) ? $hostSegments[1] : 3306;
                ConsoleOutput::info("Checking {$monitor->url}");
                \Config::set("database.connections.{$monitor->id}", [
                    'driver' => $protocol,
                    'host' => $host,
                    'port' => $port,
                    'database' => 'monitorDB',
                    'username' => 'monitorUser',
                    'password' => '',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                    'options' => [
                        \PDO::ATTR_TIMEOUT => config('laravel-uptime-monitor.uptime_check.timeout_per_connection'),
                    ],
                ]);
                try {
                    \DB::connection($monitor->id)->reconnect();
                    $monitor->uptimeRequestSucceeded(new Response(200, [], "Could reach {$monitor->url}"));
                } catch (\Exception $exception) {
                    if (str_contains($exception->getMessage(), 'time')) {
                        $monitor->uptimeRequestFailed($exception->getMessage());
                    } else {
                        $monitor->uptimeRequestSucceeded(new Response(200, [], "Could reach {$monitor->url}"));
                    }
                }
            });
            yield $promise;
        }
    }
}
