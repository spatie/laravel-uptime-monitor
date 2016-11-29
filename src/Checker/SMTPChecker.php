<?php
namespace Spatie\UptimeMonitor\Checker;


use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\MonitorCollection;

class SMTPChecker extends Checker
{

    /**
     * @inheritDoc
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
                $monitor->uptimeRequestSucceeded(new Response(200, [], "Could reach {$monitor->url}"));
            },
            'rejected' => function ($exception, $index) {
                $monitor = $this->monitors->getMonitorAtIndex($index);
                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");
                $monitor->uptimeRequestFailed($exception->getMessage());
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
                $port = (array_key_exists(1, $hostSegments)) ? $hostSegments[1] : 25;
                ConsoleOutput::info("Checking {$monitor->url}");
                try {
                    $smtpTransport = \Swift_SmtpTransport::newInstance($host, $port);
                    $smtpTransport->setTimeout(config('laravel-uptime-monitor.uptime_check.timeout_per_connection'));
                    $smtpTransport->start();
                } catch (\Swift_TransportException $e) {
                    throw new \Exception($e->getMessage());
                }
            });
            yield $promise;
        }

    }
}