<?php

namespace Spatie\UptimeMonitor\Test\Events;

use Artisan;
use Spatie\UptimeMonitor\Events\SiteDown;
use Spatie\UptimeMonitor\Models\Site;
use Event;
use Spatie\UptimeMonitor\SiteRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class SiteDownTest extends TestCase
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
    public function the_down_event_will_be_fired_when_the_uptime_check_failed_for_the_configured_amount_of_times()
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

        Event::fake();

        Event::assertFired(SiteDown::class, function ($event) {
            return $event->site->id === $this->site->id;
        });
    }


}