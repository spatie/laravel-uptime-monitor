<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;

class UptimeCheckSucceededTest extends TestCase
{
    protected $monitor;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->monitor = Monitor::factory()->create();
    }

    /** @test */
    public function the_succeeded_event_will_be_fired_when_an_uptime_check_succeeds()
    {
        MonitorRepository::getForUptimeCheck()->checkUptime();

        Event::assertDispatched(UptimeCheckSucceeded::class, function ($event) {
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

        Event::assertDispatched(UptimeCheckSucceeded::class);
    }

    /** @test */
    public function the_uptime_checker_will_succeed_with_configured_guzzle_options()
    {
        $this->server->up();
        $this->server->setResponseBody('', 301);

        Config::set('uptime-monitor.uptime_check.guzzle_options', [
            'allow_redirects' => false,
        ]);

        $monitors = MonitorRepository::getForUptimeCheck();
        $monitors->checkUptime();

        Config::set('uptime-monitor.uptime_check.guzzle_options', []);

        Event::assertDispatched(UptimeCheckSucceeded::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }
}
