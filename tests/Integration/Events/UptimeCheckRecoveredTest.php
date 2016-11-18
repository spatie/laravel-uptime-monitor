<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class UptimeCheckRecoveredTest extends TestCase
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
    public function the_recovered_event_will_be_fired_when_an_uptime_check_succeeds_after_it_has_failed()
    {
        $monitors = MonitorRepository::getForUptimeCheck();

        $this->server->down();

        $consecutiveFailsNeeded = config('laravel-uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();
        }
        $this->monitor = $this->monitor->fresh();

        $downTimeLengthInMinutes = 10;
        $this->progressMinutes($downTimeLengthInMinutes);
        $this->server->up();

        Event::assertNotFired(UptimeCheckRecovered::class);

        $monitors->checkUptime();

        Event::assertFired(UptimeCheckRecovered::class, function ($event) use ($downTimeLengthInMinutes) {
            if ($event->monitor->id !== $this->monitor->id) {
                return false;
            };

            if ($event->uptimeCheckStartedFailingOnDate->toDayDateTimeString() !== Carbon::now()->subMinutes($downTimeLengthInMinutes)->toDayDateTimeString()) {
                return false;
            }

            return true;
        });
    }
}
