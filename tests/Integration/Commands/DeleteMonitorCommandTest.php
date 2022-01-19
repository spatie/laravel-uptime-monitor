<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Mockery as m;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class DeleteMonitorCommandTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Commands\DeleteMonitor|m\Mock */
    protected $command;

    /** @var string */
    protected $url;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = m::mock('Spatie\UptimeMonitor\Commands\DeleteMonitor[confirm]');

        $this->app->bind('command.monitor:delete', function () {
            return $this->command;
        });

        $this->url = 'https://mysite.com';

        Monitor::factory()->create(['url' => $this->url]);
    }

    /** @test */
    public function it_can_delete_a_monitor()
    {
        $this->assertEquals(1, Monitor::where('url', $this->url)->count());

        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with("Are you sure you want stop monitoring {$this->url}?")
            ->andReturn('yes');

        Artisan::call('monitor:delete', ['url' => $this->url]);

        $this->assertEquals(0, Monitor::where('url', $this->url)->count());
    }
}
