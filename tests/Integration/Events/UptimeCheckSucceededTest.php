<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Spatie\UptimeMonitor\Events\MonitorSucceeded;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class UptimeCheckSucceededTest extends TestCase
{
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->monitor = factory(Monitor::class)->create();
    }

    /** @test */
    public function the_succeeded_event_will_be_fired_when_an_uptime_check_succeeds()
    {
        MonitorRepository::getForUptimeCheck()->checkUptime();

        Event::assertFired(UptimeCheckSucceeded::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }

    /** @test */
    public function the_succeed_event_will_be_fired_when_a_site_is_up_and_the_look_for_string_is_found_on_the_response()
    {
        $this->server->setResponseBody('Hi, welcome on the page');

        $this->monitor->look_for_string = 'welcome';
        $this->monitor->save();

        MonitorRepository::getForUptimeCheck()->checkUptime();

        Event::assertFired(UptimeCheckSucceeded::class);
    }
}
