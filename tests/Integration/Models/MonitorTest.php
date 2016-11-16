<?php

namespace Spatie\UptimeMonitor\Test\Integration\Models;

use Spatie\UptimeMonitor\Exceptions\CannotSaveMonitor;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        $this->monitor = factory(Monitor::class)->create(['url' => 'http://mysite.com']);
    }

    /** @test */
    public function it_will_throw_an_exception_when_creating_a_monitor_that_already_exists()
    {
        $this->expectException(CannotSaveMonitor::class);

        factory(Monitor::class)->create(['url' => 'http://mysite.com']);
    }

    /** @test */
    public function it_will_throw_an_exception_when_updating_a_monitor_to_an_url_of_a_monitor_that_already_exists()
    {
        $monitor = factory(Monitor::class)->create(['url' => 'http://myothersite.com']);

        $this->expectException(CannotSaveMonitor::class);

        $monitor->url = 'http://mysite.com';

        $monitor->save();
    }

    /** @test */
    public function it_can_disable_and_enable_itself()
    {
        $this->assertTrue($this->monitor->enabled);

        $this->monitor->disable();

        $this->monitor = $this->monitor->fresh();

        $this->assertFalse($this->monitor->enabled);

        $this->monitor->enable();

        $this->monitor = $this->monitor->fresh();

        $this->assertTrue($this->monitor->enabled);
    }
}
