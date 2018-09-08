<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;

class ListMonitorsCommandTest extends TestCase
{
    public function it_display_a_message_when_no_monitors_are_configured()
    {
        Artisan::call('monitor:list');

        $output = Artisan::output();

        $this->seeInConsoleOutput($output, 'There are no monitors created or enabled');
        $this->dontSeeInConsoleOutput($output, 'Healthy monitors');
    }

    /** @test */
    public function it_can_show_monitors_that_have_not_been_checked_yet()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput(Artisan::output(), [
            'Not yet checked',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_healthy_monitors()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::UP]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput(Artisan::output(), [
            'Healthy monitors',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_monitors_with_failing_uptime_checks()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::DOWN]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput(Artisan::output(), [
            'Uptime check failed',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_monitors_that_have_certificate_problems()
    {
        $monitor = factory(Monitor::class)->create([
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::INVALID,
        ]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput(Artisan::output(), [
            'Certificate check failed',
            $monitor->url,
        ]);
    }

    /** @test */
    public function it_can_show_disabled_monitors()
    {
        $monitor = factory(Monitor::class)->create([
            'uptime_check_enabled' => false,
        ]);

        Artisan::call('monitor:list');

        $this->seeInConsoleOutput(Artisan::output(), [
            'Disabled monitors',
            $monitor->url,
        ]);
    }
}
