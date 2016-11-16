<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckUptimeCommandTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_perform_uptime_checks()
    {
        $monitor = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('sites:check-uptime');

        $monitor = $monitor->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor->uptime_status);
    }

    /** @test */
    public function it_can_check_the_uptime_of_a_specific_site()
    {
        $monitor1 = factory(Monitor::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $monitor2 = factory(Monitor::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        Artisan::call('sites:check-uptime', ['--url' => $monitor1->url]);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor1->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $monitor2->uptime_status);
    }

    /** @test */
    public function it_can_check_the_uptime_of_multiple_specific_sites()
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

        Artisan::call('sites:check-uptime', ['--url' => $monitor1->url.','.$monitor2->url]);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();
        $monitor3 = $monitor3->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor1->uptime_status);
        $this->assertEquals(UptimeStatus::UP, $monitor2->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $monitor3->uptime_status);
    }
}
