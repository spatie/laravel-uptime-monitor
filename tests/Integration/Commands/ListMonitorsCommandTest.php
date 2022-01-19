<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class ListMonitorsCommandTest extends TestCase
{
    public function it_display_a_message_when_no_monitors_are_configured()
    {
        Artisan::call('monitor:list');

        $this->seeInConsoleOutput('There are no monitors created or enabled');
        $this->dontSeeInConsoleOutput('Healthy monitors');
    }

    /** @test */
    public function it_can_show_monitors_that_have_not_been_checked_yet()
    {
        $monitor = Monitor::factory()->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Not yet checked',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_healthy_monitors()
    {
        $monitor = Monitor::factory()->create(['uptime_status' => UptimeStatus::UP]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Healthy monitors',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_monitors_with_failing_uptime_checks()
    {
        $monitor = Monitor::factory()->create(['uptime_status' => UptimeStatus::DOWN]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Uptime check failed',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_monitors_that_have_certificate_problems()
    {
        $monitor = Monitor::factory()->create([
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::INVALID,
        ]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Certificate check failed',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_disabled_monitors()
    {
        $monitor = Monitor::factory()->create([
            'uptime_check_enabled' => false,
        ]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Disabled monitors',
            $monitor->url,
        ]);
    }
}
