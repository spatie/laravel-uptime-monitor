<?php

namespace Spatie\UptimeMonitor\Test\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckUptimeCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_a_command_to_perform_uptime_checks()
    {
        $site = factory(Site::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('sites:check-uptime');

        $site = $site->fresh();

        $this->assertEquals(UptimeStatus::UP, $site->uptime_status);
    }

    /** @test */
    public function it_can_check_a_specific_site()
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
}
