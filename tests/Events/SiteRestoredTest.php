<?php

namespace Spatie\UptimeMonitor\Test\Events;

use Artisan;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Event;
use Spatie\UptimeMonitor\SiteRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class SiteRestoredTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function the_restored_event_will_be_fired_when_a_down_site_is_restored()
    {
        $sites = SiteRepository::getAllForUptimeCheck();

        $this->server->down();

        $consecutiveFailsNeeded = config('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures');

        foreach(range(1, $consecutiveFailsNeeded) as $index){
            $sites->checkUptime();
        }

        $this->site = $this->site->fresh();

        $this->server->up();

        Event::assertNotFired(SiteRestored::class);

        $sites->checkUptime();

        Event::assertFired(SiteRestored::class, function ($event) {
            return $event->site->id === $this->site->id;
        });

    }
}