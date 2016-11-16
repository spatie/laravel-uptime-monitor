<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Spatie\UptimeMonitor\Events\MonitorFailed;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorFailedTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->monitor = factory(Monitor::class)->create();
    }

    /** @test */
    public function the_down_event_will_be_fired_when_the_uptime_check_failed_for_the_configured_amount_of_times()
    {
        $this->server->down();

        $monitors = MonitorRepository::getForUptimeCheck();

        $consecutiveFailsNeeded = config('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();

            if ($index < $consecutiveFailsNeeded) {
                Event::assertNotFired(MonitorFailed::class);
            }
        }

        Event::assertFired(MonitorFailed::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }

    /** @test */
    public function it_will_fire_the_faied_event_again_if_a_monitor_keeps_failing_after_the_configured_amount_of_minutes()
    {
        $this->server->down();

        $monitors = MonitorRepository::getForUptimeCheck();

        $consecutiveFailsNeeded = config('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();

            if ($index < $consecutiveFailsNeeded) {
                Event::assertNotFired(MonitorFailed::class);
            }
        }

        Event::assertFired(MonitorFailed::class);

        $this->resetEventAssertions();

        $monitors->checkUptime();

        Event::assertNotFired(MonitorFailed::class);

        $this->resetEventAssertions();

        $this->progressMinutes(config('laravel-uptime-monitor.notifications.resend_down_notification_every_minutes'));

        $monitors->checkUptime();

        Event::assertFired(MonitorFailed::class);
    }

    /** @test */
    public function the_failing_event_will_be_fired_when_a_site_is_but_the_look_for_string_is_not_found_on_the_response()
    {
        $this->server->setResponseBody('Hi, welcome on the page');

        $this->monitor->look_for_string = 'Another page';
        $this->monitor->save();

        $this->app['config']->set('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures', 1);

        MonitorRepository::getForUptimeCheck()->checkUptime();

        Event::assertFired(MonitorFailed::class);
    }
}
