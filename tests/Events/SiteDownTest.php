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
    }

    /** @test */
    public function it_will_fire_the_down_event_again_if_a_site_after_the_configured_amount_of_minutes_if_the_site_stays_down()
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

        Event::assertFired(SiteDown::class);

        $this->resetEventAssertions();

        $sites->checkUptime();

        Event::assertNotFired(SiteDown::class);

        $this->resetEventAssertions();

        $this->progressMinutes(config('laravel-uptime-monitor.notifications.resend_down_notification_every_minutes'));

        $sites->checkUptime();

        Event::assertFired(SiteDown::class);
    }

    /** @test */
    public function the_down_event_will_be_fired_when_a_site_is_up_but_the_look_for_string_is_not_found_on_the_response()
    {
        $this->server->setResponseBody("Hi, welcome on the page");

        $this->site->look_for_string = 'Another page';
        $this->site->save();

        $this->app['config']->set('laravel-uptime-monitor.fire_down_event_after_consecutive_failed_checks', 1);

        $sites = SiteRepository::getAllForUptimeCheck();

        $sites->checkUptime();

        Event::assertFired(SiteDown::class);
    }




}