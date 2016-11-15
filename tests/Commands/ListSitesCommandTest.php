<?php

namespace Spatie\UptimeMonitor\Test\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class ListSitesCommandTest extends TestCase
{
    public function it_display_a_message_when_no_sites_are_configured()
    {
        Artisan::call('sites:list');

        $this->seeInConsoleOutput("There are no sites configured or enabled");
        $this->dontSeeInConsoleOutput("Healthy sites");
    }

    /** @test */
    public function it_can_show_healthy_sites()
    {
        $site = factory(Site::class)->create(['uptime_status' => UptimeStatus::UP]);

        Artisan::call('sites:list');

        $this->seeInConsoleOutput([
            "Healthy sites",
            $site->url
        ]);
    }


}
