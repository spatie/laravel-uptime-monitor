<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Illuminate\Support\Facades\Artisan;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class EnableMonitorCommandTest extends TestCase
{
    /** @test */
    public function it_can_enable_a_disabled_monitor()
    {
        $monitor = Monitor::factory()->create([
            'uptime_check_enabled' => false,
            'certificate_check_enabled' => false,
            'url' => 'https://mysite.com',
        ]);

        $this->assertFalse($monitor->fresh()->uptime_check_enabled);

        $this->artisan('monitor:enable', ['url' => 'https://mysite.com']);

        $monitor = $monitor->fresh();

        $this->assertTrue($monitor->uptime_check_enabled);
        $this->assertTrue($monitor->certificate_check_enabled);
    }

    /** @test */
    public function it_will_only_not_enable_the_uptime_check_if_the_url_starts_with_http()
    {
        $monitor = Monitor::factory()->create([
            'uptime_check_enabled' => false,
            'certificate_check_enabled' => false,
            'url' => 'http://mysite.com',
        ]);

        $this->assertFalse($monitor->fresh()->uptime_check_enabled);

        $this->artisan('monitor:enable', ['url' => 'http://mysite.com']);

        $monitor = $monitor->fresh();

        $this->assertTrue($monitor->uptime_check_enabled);
        $this->assertFalse($monitor->certificate_check_enabled);
    }

    /** @test */
    public function it_displays_a_message_if_the_monitor_is_not_found()
    {
        Artisan::call('monitor:enable', ['url' => 'https://mysite.com']);

        $this->seeInConsoleOutput('There is no monitor configured for url');
    }

    /** @test */
    public function it_can_enable_multiple_urls_at_once()
    {
        $monitor1 = Monitor::factory()->create([
            'uptime_check_enabled' => false,
            'certificate_check_enabled' => false,
            'url' => 'https://mysite.com',
        ]);

        $monitor2 = Monitor::factory()->create([
            'uptime_check_enabled' => false,
            'certificate_check_enabled' => false,
            'url' => 'http://mysite2.com',
        ]);

        $this->artisan('monitor:enable', ['url' => 'https://mysite.com, http://mysite2.com']);

        $this->assertTrue($monitor1->fresh()->uptime_check_enabled);
        $this->assertTrue($monitor2->fresh()->uptime_check_enabled);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();

        $this->assertTrue($monitor1->uptime_check_enabled);
        $this->assertTrue($monitor1->certificate_check_enabled);
        $this->assertTrue($monitor2->uptime_check_enabled);
        $this->assertFalse($monitor2->certificate_check_enabled);
    }
}
