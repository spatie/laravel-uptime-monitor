<?php

namespace Spatie\UptimeMonitor\Test\Events;

use Artisan;
use Spatie\UptimeMonitor\Events\SiteDown;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Event;
use Spatie\UptimeMonitor\SiteRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class SiteDownTest extends TestCase
{
    protected $site;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function it_will_fire_the_up_event_when_a_site_is_up()
    {
        $this->server->down();

        $sites = SiteRepository::getAllForUptimeCheck();

        $consecutiveFailsNeeded = config('laravel-uptime-monitor.fire_down_event_after_consecutive_failed_checks');

        foreach(range(1, $consecutiveFailsNeeded) as $index){
            $sites->checkUptime();

            if ($index < $consecutiveFailsNeeded) {
                Event::assertNotFired(SiteDown::class);
            }
        }

        Event::assertFired(SiteDown::class, function ($event) {
            return $event->site->id === $this->site->id;
        });

    }
}