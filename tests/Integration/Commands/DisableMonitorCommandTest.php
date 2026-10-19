<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use PHPUnit\Framework\Attributes\Test;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class DisableMonitorCommandTest extends TestCase
{
    #[Test]
    public function it_can_disable_an_enabled_monitor()
    {
        $monitor = Monitor::factory()->create([
            'uptime_check_enabled' => true,
            'url' => 'http://mysite.com',
        ]);

        $this->assertTrue($monitor->fresh()->uptime_check_enabled);

        $this->artisan('monitor:disable', ['url' => 'http://mysite.com']);

        $this->assertFalse($monitor->fresh()->uptime_check_enabled);
    }

    #[Test]
    public function it_displays_a_message_if_the_monitor_is_not_found()
    {
        Artisan::call('monitor:disable', ['url' => 'https://mysite.com']);

        $this->seeInConsoleOutput('There is no monitor configured for url');
    }

    #[Test]
    public function it_can_disable_multiple_urls_at_once()
    {
        $monitor1 = Monitor::factory()->create([
            'uptime_check_enabled' => true,
            'url' => 'http://mysite.com',
        ]);

        $monitor2 = Monitor::factory()->create([
            'uptime_check_enabled' => true,
            'url' => 'http://mysite2.com',
        ]);

        $this->artisan('monitor:disable', ['url' => 'http://mysite.com, http://mysite2.com']);

        $this->assertFalse($monitor1->fresh()->uptime_check_enabled);
        $this->assertFalse($monitor2->fresh()->uptime_check_enabled);
    }
}
