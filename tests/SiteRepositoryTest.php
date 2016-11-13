<?php

namespace Spatie\UptimeMonitor\Test;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;
use Spatie\Url\Url;

class SiteRepositoryTest extends TestCase
{
    /** @test */
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_get_all_sites_that_are_down()
    {
        Site::create(['url' => 'http://down1.com', 'uptime_status' => UptimeStatus::DOWN]);

        Site::create(['url' => 'http://up.com', 'uptime_status' => UptimeStatus::UP]);

        Site::create(['url' => 'http://down2.com', 'uptime_status' => UptimeStatus::DOWN]);

        $downSites = SiteRepository::downSites();

        $this->assertEquals(['http://down1.com', 'http://down2.com'], $this->getSiteUrls($downSites));
    }

    protected function getSiteUrls(Collection $sites)
    {
        return $sites
            ->pluck('url')
            ->map(function(Url $url) {
               return trim($url, '/');
            })
        ->toArray();
    }
}
