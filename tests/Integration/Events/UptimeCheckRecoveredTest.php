<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class UptimeCheckRecoveredTest extends TestCase
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
    public function the_recovered_event_will_be_fired_when_an_uptime_check_succeeds_after_it_has_failed()
    {
        $monitors = MonitorRepository::getForUptimeCheck();

        $this->server->down();

        $consecutiveFailsNeeded = config('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();
        }

        $this->monitor = $this->monitor->fresh();

        $downTimeLengthInMinutes = 10;
        $this->progressMinutes($downTimeLengthInMinutes);

        $this->server->up();

        Event::assertNotDispatched(UptimeCheckRecovered::class);

        $monitors->checkUptime();

        Event::assertDispatched(UptimeCheckRecovered::class, function (UptimeCheckRecovered $event) use ($downTimeLengthInMinutes) {
            if ($event->monitor->id !== $this->monitor->id) {
                return false;
            }

            if ($event->downtimePeriod->startDateTime->toDayDateTimeString() !== Carbon::now()->subMinutes($downTimeLengthInMinutes)->toDayDateTimeString()) {
                return false;
            }

            return true;
        });
    }
}
