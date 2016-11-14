<?php

namespace Spatie\UptimeMonitor\Test\Commands;

use Artisan;
use Mockery as  m;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class CreateSiteCommandTest extends TestCase
{
    /**
     * @var \Spatie\UptimeMonitor\Commands\CreateSite|m\Mock
     */
    protected $command;

    public function setUp()
    {
        parent::setUp();

        $this->command = m::mock('Spatie\UptimeMonitor\Commands\CreateSite[ask, confirm]');

        $this->app->bind('command.sites:create', function () {
            return $this->command;
        });
    }

    /** @test */
    public function it_can_create_a_https_site()
    {
        $this->command
            ->shouldReceive('ask')
            ->once()
            ->with('/Which url to you want to monitor/')
            ->andReturn('https://mysite.com');

        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with('/Should we look for a specific string on the response/')
            ->andReturn('');

        Artisan::call('sites:create');

        $site = Site::where('url', 'https://mysite.com')->first();

        $this->assertSame($site->uptime_status, UptimeStatus::NOT_YET_CHECKED);
        $this->assertTrue($site->check_ssl_certificate);
    }

    /** @test */
    public function it_can_create_a_http_site()
    {
        $this->command
            ->shouldReceive('ask')
            ->once()
            ->with('/Which url to you want to monitor/')
            ->andReturn('http://mysite.com');

        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with('/Should we look for a specific string on the response/')
            ->andReturn('');

        Artisan::call('sites:create');

        $site = Site::where('url', 'http://mysite.com')->first();

        $this->assertSame($site->uptime_status, UptimeStatus::NOT_YET_CHECKED);
        $this->assertFalse($site->check_ssl_certificate);
    }
}
