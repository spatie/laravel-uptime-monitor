<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class UptimeCheckFailedTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->monitor = Monitor::factory()->create();
    }

    /** @test */
    public function the_down_event_will_be_fired_when_the_uptime_check_failed_for_the_configured_amount_of_times()
    {
        $this->server->down();

        $monitors = MonitorRepository::getForUptimeCheck();

        $consecutiveFailsNeeded = config('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();

            if ($index < $consecutiveFailsNeeded) {
                Event::assertNotDispatched(UptimeCheckFailed::class);
            }
        }

        Event::assertDispatched(UptimeCheckFailed::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }

    /** @test */
    public function it_will_fire_the_failed_event_again_if_a_monitor_keeps_failing_after_the_configured_amount_of_minutes()
    {
        $this->server->down();

        $monitors = MonitorRepository::getForUptimeCheck();

        $consecutiveFailsNeeded = config('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();

            if ($index < $consecutiveFailsNeeded) {
                Event::assertNotDispatched(UptimeCheckFailed::class);
            }
        }

        Event::assertDispatched(UptimeCheckFailed::class);

        $this->resetEventAssertions();

        $monitors->checkUptime();

        Event::assertNotDispatched(UptimeCheckFailed::class);

        $this->resetEventAssertions();

        $this->progressMinutes(config('uptime-monitor.notifications.resend_uptime_check_failed_notification_every_minutes'));

        $monitors->checkUptime();

        Event::assertDispatched(UptimeCheckFailed::class);
    }

    /** @test */
    public function the_failing_event_will_be_fired_when_a_site_is_but_the_look_for_string_is_not_found_on_the_response()
    {
        $this->server->setResponseBody('Hi, welcome on the page');

        $this->monitor->look_for_string = 'Another page';
        $this->monitor->save();

        $this->app['config']->set('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures', 1);

        MonitorRepository::getForUptimeCheck()->checkUptime();

        Event::assertDispatched(UptimeCheckFailed::class);

        Event::assertNotDispatched(UptimeCheckSucceeded::class);

        Event::assertNotDispatched(UptimeCheckRecovered::class);
    }

    /** @test */
    public function the_uptime_checker_will_fail_without_configured_guzzle_options()
    {
        $this->server->up();
        $this->server->setResponseBody('', 301);

        $monitors = MonitorRepository::getForUptimeCheck();

        $consecutiveFailsNeeded = config('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();

            if ($index < $consecutiveFailsNeeded) {
                Event::assertNotDispatched(UptimeCheckFailed::class);
            }
        }

        Event::assertDispatched(UptimeCheckFailed::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }
}
