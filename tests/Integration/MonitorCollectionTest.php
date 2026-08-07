<?php

namespace Spatie\UptimeMonitor\Test\Integration;

use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\Integration\Helpers\SendUptimeCheckRequestActionFake;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorCollectionTest extends TestCase
{
    /** @test */
    public function it_uses_the_action_configured_to_send_the_uptime_check_request()
    {
        config()->set(
            'uptime-monitor.actions.send_uptime_check_request',
            SendUptimeCheckRequestActionFake::class
        );

        $monitor = Monitor::factory()->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://this-url-is-never-requested.test',
            'look_for_string' => SendUptimeCheckRequestActionFake::RESPONSE_BODY,
        ]);

        MonitorRepository::getForUptimeCheck()->checkUptime();

        $this->assertEquals(UptimeStatus::UP, $monitor->fresh()->uptime_status);
    }
}
