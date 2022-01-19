<?php

namespace Spatie\UptimeMonitor\Test\Integration\Models;

use Spatie\UptimeMonitor\Exceptions\CannotSaveMonitor;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp(): void
    {
        parent::setUp();

        $this->monitor = Monitor::factory()->create([
            'url' => 'http://mysite.com',
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
        ]);
    }

    /** @test */
    public function it_will_throw_an_exception_when_creating_a_monitor_that_already_exists()
    {
        $this->expectException(CannotSaveMonitor::class);

        Monitor::factory()->create(['url' => 'http://mysite.com']);
    }

    /** @test */
    public function it_will_throw_an_exception_when_updating_a_monitor_to_an_url_of_a_monitor_that_already_exists()
    {
        $monitor = Monitor::factory()->create(['url' => 'http://myothersite.com']);

        $this->expectException(CannotSaveMonitor::class);

        $monitor->url = 'http://mysite.com';

        $monitor->save();
    }

    /** @test */
    public function it_can_disable_and_enable_itself_for_an_http_url()
    {
        $this->monitor->disable();

        $this->monitor = $this->monitor->fresh();

        $this->assertFalse($this->monitor->uptime_check_enabled);
        $this->assertFalse($this->monitor->certificate_check_enabled);

        $this->monitor->enable();

        $this->monitor = $this->monitor->fresh();

        $this->assertTrue($this->monitor->uptime_check_enabled);

        //it will not enable the certificate check for a non-https site.
        $this->assertFalse($this->monitor->certificate_check_enabled);
    }

    /** @test */
    public function raw_url_is_appended_during_serialization()
    {
        $this->assertEquals(
            'http://mysite.com',
            $this->monitor->toArray()['raw_url']
        );
    }
}
