<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

class CheckUptimeCommandTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_perform_uptime_checks()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('monitor:check-uptime');

        $monitor = $monitor->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor->uptime_status);
    }

    /** @test */
    public function it_can_perform_an_uptime_check_for_specific_monitor()
    {
        $monitor1 = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $monitor2 = factory(Monitor::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        Artisan::call('monitor:check-uptime', ['--url' => $monitor1->url]);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor1->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $monitor2->uptime_status);
    }

    /** @test */
    public function it_can_perform_an_uptime_checks_for_a_set_of_specific_monitors()
    {
        $this->skipIfNotConnectedToTheInternet();

        $monitor1 = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $monitor2 = factory(Monitor::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        $monitor3 = factory(Monitor::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://bing.com',
        ]);

        Artisan::call('monitor:check-uptime', ['--url' => $monitor1->url.','.$monitor2->url]);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();
        $monitor3 = $monitor3->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor1->uptime_status);
        $this->assertEquals(UptimeStatus::UP, $monitor2->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $monitor3->uptime_status);
    }
}
