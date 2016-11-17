<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
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
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Monitors that have not been checked yet',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_healthy_monitors()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::UP]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Healthy monitors',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_monitors_with_failing_uptime_checks()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::DOWN]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Monitors that have failed',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_monitors_that_have_detected_ssl_problems()
    {
        $monitor = factory(Monitor::class)->create([
            'ssl_certificate_check_enabled' => true,
            'ssl_certificate_status' => SslCertificateStatus::INVALID,
        ]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Monitors reporting SSL certificate problems',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_disabled_monitors()
    {
        $monitor = factory(Monitor::class)->create([
            'enabled' => false,
        ]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput([
            'Monitors that have been disabled',
            $monitor->url,
        ]);
    }
}
