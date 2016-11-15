<?php

namespace Spatie\UptimeMonitor\Test\Events;

use Artisan;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Models\Site;
use Event;
use Spatie\UptimeMonitor\SiteRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class SiteUpTest extends TestCase
{
    protected $site;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function the_up_event_will_be_fired_when_a_site_is_up()
    {
        SiteRepository::getAllForUptimeCheck()->checkUptime();

        Event::assertFired(SiteUp::class, function ($event) {
            return $event->site->id === $this->site->id;
        });
    }

    /** @test */
    public function the_down_event_will_be_fired_when_a_site_is_up_and_the_look_for_string_is_found_on_the_response()
    {
        $this->server->setResponseBody("Hi, welcome on the page");

        $this->site->look_for_string = 'welcome';
        $this->site->save();

        SiteRepository::getAllForUptimeCheck()->checkUptime();

        Event::assertFired(SiteUp::class);
    }
}