<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Spatie\UptimeMonitor\Events\MonitorRecovered;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class SiteRestoredTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->site = factory(Monitor::class)->create();
    }

    /** @test */
    public function the_restored_event_will_be_fired_when_a_down_site_is_restored()
    {
        $monitors = MonitorRepository::getAllForUptimeCheck();

        $this->server->down();

        $consecutiveFailsNeeded = config('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures');

        foreach (range(1, $consecutiveFailsNeeded) as $index) {
            $monitors->checkUptime();
        }

        $this->site = $this->site->fresh();

        $this->server->up();

        Event::assertNotFired(MonitorRecovered::class);

        $monitors->checkUptime();

        Event::assertFired(MonitorRecovered::class, function ($event) {
            return $event->site->id === $this->site->id;
        });
    }
}
