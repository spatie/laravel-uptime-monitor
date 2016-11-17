<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class EnableMonitorCommandTest extends TestCase
{
    /** @test */
    public function it_can_enable_a_disabled_monitor()
    {
        $monitor = factory(Monitor::class)->create([
            'uptime_check_enabled' => false,
            'ssl_certificate_check_enabled' => false,
            'url' => 'http://mysite.com',
        ]);

        $this->assertFalse($monitor->fresh()->uptime_check_enabled);

        $this->artisan('monitor:enable', ['url' => 'http://mysite.com']);

        $monitor = $monitor->fresh();

        $this->assertTrue($monitor->uptime_check_enabled);
        $this->assertTrue($monitor->ssl_certificate_check_enabled);
    }

    /** @test */
    public function it_displays_a_message_if_the_monitor_is_not_found()
    {
        $this->artisan('monitor:enable', ['url' => 'http://mysite.com']);

        $this->seeInConsoleOutput('There is no monitor configured for url');
    }

    /** @test */
    public function it_can_enable_multiple_urls_at_once()
    {
        $monitor1 = factory(Monitor::class)->create([
            'uptime_check_enabled' => false,
            'ssl_certificate_check_enabled' => false,
            'url' => 'http://mysite.com',
        ]);

        $monitor2 = factory(Monitor::class)->create([
            'uptime_check_enabled' => false,
            'ssl_certificate_check_enabled' => false,
            'url' => 'http://mysite2.com',
        ]);

        $this->artisan('monitor:enable', ['url' => 'http://mysite.com, http://mysite2.com']);

        $this->assertTrue($monitor1->fresh()->uptime_check_enabled);
        $this->assertTrue($monitor2->fresh()->uptime_check_enabled);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();

        $this->assertTrue($monitor1->uptime_check_enabled);
        $this->assertTrue($monitor1->ssl_certificate_check_enabled);
        $this->assertTrue($monitor2->uptime_check_enabled);
        $this->assertTrue($monitor2->ssl_certificate_check_enabled);
    }
}
