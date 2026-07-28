<?php

namespace Spatie\UptimeMonitor\Test\Integration;

use Illuminate\Support\Facades\Config;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorCollectionTest extends TestCase
{
    /** @test */
    public function it_matches_every_monitor_up_with_its_own_response()
    {
        // Keep the retries of the unreachable monitor from slowing the test down.
        Config::set('uptime-monitor.uptime_check.retry_connection_after_milliseconds', 1);

        $reachable = $this->createMonitor($this->reachableUrl('first'));
        $unreachable = $this->createMonitor('http://localhost:9987');
        $alsoReachable = $this->createMonitor($this->reachableUrl('second'));

        MonitorRepository::getForUptimeCheck()->checkUptime();

        $this->assertEquals(UptimeStatus::UP, $reachable->fresh()->uptime_status);
        $this->assertEquals(UptimeStatus::DOWN, $unreachable->fresh()->uptime_status);
        $this->assertEquals(UptimeStatus::UP, $alsoReachable->fresh()->uptime_status);

        $this->assertSame('', $reachable->fresh()->uptime_check_failure_reason);
        $this->assertNotSame('', $unreachable->fresh()->uptime_check_failure_reason);
    }

    /** @test */
    public function it_checks_monitors_when_the_collection_keys_have_gaps()
    {
        $this->createMonitor($this->reachableUrl('first'));

        $monitor = $this->createMonitor($this->reachableUrl('second'));

        MonitorRepository::getForUptimeCheck()
            ->filter(fn (Monitor $candidate) => $candidate->id === $monitor->id)
            ->checkUptime();

        $this->assertEquals(UptimeStatus::UP, $monitor->fresh()->uptime_status);
    }

    protected function createMonitor(string $url): Monitor
    {
        return Monitor::factory()->create([
            'url' => $url,
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
        ]);
    }

    protected function reachableUrl(string $name): string
    {
        return sprintf('http://localhost:%s?monitor=%s', getenv('TEST_SERVER_PORT'), $name);
    }
}
