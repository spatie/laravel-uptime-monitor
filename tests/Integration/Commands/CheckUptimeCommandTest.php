<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckUptimeCommandTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_perform_uptime_checks()
    {
        $site = factory(Site::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('sites:check-uptime');

        $site = $site->fresh();

        $this->assertEquals(UptimeStatus::UP, $site->uptime_status);
    }

    /** @test */
    public function it_can_check_the_uptime_of_a_specific_site()
    {
        $site1 = factory(Site::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $site2 = factory(Site::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        Artisan::call('sites:check-uptime', ['--url' => $site1->url]);

        $site1 = $site1->fresh();
        $site2 = $site2->fresh();

        $this->assertEquals(UptimeStatus::UP, $site1->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $site2->uptime_status);
    }

    /** @test */
    public function it_can_check_the_uptime_of_multiple_specific_sites()
    {
        $this->skipIfNotConnectedToTheInternet();

        $site1 = factory(Site::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $site2 = factory(Site::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        $site3 = factory(Site::class)->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://bing.com',
        ]);

        Artisan::call('sites:check-uptime', ['--url' => $site1->url . ',' . $site2->url]);

        $site1 = $site1->fresh();
        $site2 = $site2->fresh();
        $site3 = $site3->fresh();

        $this->assertEquals(UptimeStatus::UP, $site1->uptime_status);
        $this->assertEquals(UptimeStatus::UP, $site2->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $site3->uptime_status);
    }
}
