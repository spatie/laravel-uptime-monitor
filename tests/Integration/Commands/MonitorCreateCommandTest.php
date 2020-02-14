<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Mockery as m;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorCreateCommandTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Commands\CreateMonitor|m\Mock */
    protected $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = m::mock('Spatie\UptimeMonitor\Commands\CreateMonitor[ask, confirm]');

        $this->app->bind('command.monitor:create', function () {
            return $this->command;
        });
    }

    /** @test */
    public function it_can_create_a_monitor_for_a_https_site()
    {
        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with('Should we look for a specific string on the response?')
            ->andReturn('');

        Artisan::call('monitor:create', ['url' => 'https://mysite.com']);

        $monitor = Monitor::where('url', 'https://mysite.com')->first();

        $this->assertSame($monitor->uptime_status, UptimeStatus::NOT_YET_CHECKED);
        $this->assertTrue($monitor->certificate_check_enabled);
    }

    /** @test */
    public function it_can_create_a_monitor_for_a_http_site()
    {
        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with('Should we look for a specific string on the response?')
            ->andReturn('');

        Artisan::call('monitor:create', ['url' => 'http://mysite.com']);

        $monitor = Monitor::where('url', 'http://mysite.com')->first();

        $this->assertSame($monitor->uptime_status, UptimeStatus::NOT_YET_CHECKED);
        $this->assertFalse($monitor->certificate_check_enabled);

        $this->bringTestServerUp();
        $this->bringTestServerDown();
    }
}
