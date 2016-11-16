<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Spatie\UptimeMonitor\Events\MonitorHealthy;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorHealthyTest extends TestCase
{
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->site = factory(Monitor::class)->create();
    }

    /** @test */
    public function the_up_event_will_be_fired_when_a_site_is_up()
    {
        MonitorRepository::getAllForUptimeCheck()->checkUptime();

        Event::assertFired(MonitorHealthy::class, function ($event) {
            return $event->site->id === $this->site->id;
        });
    }

    /** @test */
    public function the_down_event_will_be_fired_when_a_site_is_up_and_the_look_for_string_is_found_on_the_response()
    {
        $this->server->setResponseBody('Hi, welcome on the page');

        $this->site->look_for_string = 'welcome';
        $this->site->save();

        MonitorRepository::getAllForUptimeCheck()->checkUptime();

        Event::assertFired(MonitorHealthy::class);
    }
}
