<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.11.16
 * Time: 11:42
 */

namespace Spatie\UptimeMonitor\Checker;


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
        foreach ($monitors as $monitor) {
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
                ConsoleOutput::info("Could reach {$monitor->url}");
                $monitor->uptimeRequestSucceeded(new Response(200, [], "Could reach {$monitor->url}"));
            } catch (\Exception $exception) {
                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");
                $monitor->uptimeRequestFailed($exception->getMessage());
            }
        }
    }
}