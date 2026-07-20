<?php

namespace Spatie\UptimeMonitor\Test\Integration;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\Attributes\Test;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorCollectionTest extends TestCase
{
    #[Test]
    public function it_does_not_retry_when_retries_is_zero()
    {
        $this->app['config']->set('uptime-monitor.uptime_check.retries', 0);

        $history = [];
        $mockHandler = new MockHandler([
            new ConnectException('Connection refused', new Request('GET', 'http://failing-url.test')),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $stack->push(Middleware::history($history));

        $this->app['config']->set('uptime-monitor.uptime_check.guzzle_options', ['handler' => $stack]);

        Monitor::factory()->create(['url' => 'http://failing-url.test', 'uptime_check_enabled' => true]);

        MonitorRepository::getForUptimeCheck()->checkUptime();

        $this->assertCount(1, $history);
    }
}
