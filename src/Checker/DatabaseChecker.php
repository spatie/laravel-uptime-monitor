<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.11.16
 * Time: 11:15
 */

namespace Spatie\UptimeMonitor\Checker;


use GuzzleHttp\Psr7\Response;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\MonitorCollection;

class DatabaseChecker extends Checker
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
            $port = (array_key_exists(1, $hostSegments)) ? $hostSegments[1] : 3306;
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
                'options' => array(
                    \PDO::ATTR_TIMEOUT => config('laravel-uptime-monitor.uptime_check.timeout_per_connection'),
                ),
            ]);
            ConsoleOutput::info("Checking {$monitor->url}");
            try {
                \DB::connection($monitor->id)->reconnect();
                ConsoleOutput::info("Could reach {$monitor->url}");
                $monitor->uptimeRequestSucceeded(new Response(200, [], "Could reach {$monitor->url}"));
            } catch (\Exception $exception) {
                if (str_contains($exception->getMessage(), 'time')) {
                    ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");
                    $monitor->uptimeRequestFailed($exception->getMessage());
                } else {
                    ConsoleOutput::info("Could reach {$monitor->url}");
                    $monitor->uptimeRequestSucceeded(new Response(200, [], "Could reach {$monitor->url}"));
                }
            }
        }
    }
}