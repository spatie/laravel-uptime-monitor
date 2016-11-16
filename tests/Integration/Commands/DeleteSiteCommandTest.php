<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Mockery as m;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class DeleteSiteCommandTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Commands\DeleteMonitor|m\Mock */
    protected $command;

    /** @var string */
    protected $url;

    public function setUp()
    {
        parent::setUp();

        $this->command = m::mock('Spatie\UptimeMonitor\Commands\DeleteSite[confirm]');

        $this->app->bind('command.monitor:delete', function () {
            return $this->command;
        });

        $this->url = 'https://mysite.com';

        factory(Monitor::class)->create(['url' => $this->url]);
    }

    /** @test */
    public function it_can_delete_a_site()
    {
        $this->assertEquals(1, Monitor::where('url', $this->url)->count());

        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with('/Are you sure you want stop monitoring/')
            ->andReturn('yes');

        Artisan::call('monitor:delete', ['url' => $this->url]);

        $this->assertEquals(0, Monitor::where('url', $this->url)->count());
    }
}
