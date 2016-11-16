<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class ListSitesCommandTest extends TestCase
{
    public function it_display_a_message_when_no_sites_are_configured()
    {
        Artisan::call('sites:list');

        $this->seeInConsoleOutput('There are no sites configured or enabled');
        $this->dontSeeInConsoleOutput('Healthy sites');
    }

    /** @test */
    public function it_can_show_sites_that_have_not_been_checked_yet()
    {
        $site = factory(Site::class)->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('sites:list');

        $this->seeInConsoleOutput([
            'Sites that have not been checked yet',
            $site->url,
        ]);
    }

    /** @test */
    public function it_can_show_healthy_sites()
    {
        $site = factory(Site::class)->create(['uptime_status' => UptimeStatus::UP]);

        Artisan::call('sites:list');

        $this->seeInConsoleOutput([
            'Healthy sites',
            $site->url,
        ]);
    }

    /** @test */
    public function it_can_show_sites_that_are_down()
    {
        $site = factory(Site::class)->create(['uptime_status' => UptimeStatus::DOWN]);

        Artisan::call('sites:list');

        $this->seeInConsoleOutput([
            'Sites that are down',
            $site->url,
        ]);
    }

    /** @test */
    public function it_can_show_sites_with_ssl_problems()
    {
        $site = factory(Site::class)->create([
            'check_ssl_certificate' => true,
            'ssl_certificate_status' => SslCertificateStatus::INVALID,
        ]);

        Artisan::call('sites:list');

        $this->seeInConsoleOutput([
            'Sites with ssl certificate problems',
            $site->url,
        ]);
    }
}
