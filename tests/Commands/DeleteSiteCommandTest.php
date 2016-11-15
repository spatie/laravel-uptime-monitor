<?php

namespace Spatie\UptimeMonitor\Test\Commands;

use Artisan;
use Mockery as  m;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class DeleteSiteCommandTest extends TestCase
{
    /**
     * @var \Spatie\UptimeMonitor\Commands\CreateSite|m\Mock
     */
    protected $command;

    /** @var string */
    protected $url;

    public function setUp()
    {
        parent::setUp();

        $this->command = m::mock('Spatie\UptimeMonitor\Commands\DeleteSite[confirm]');

        $this->app->bind('command.sites:delete', function () {
            return $this->command;
        });

        $this->url = 'https://mysite.com';

        factory(Site::class)->create(['url' => $this->url]);
    }

    /** @test */
    public function it_can_delete_a_site()
    {
        $this->assertEquals(1, Site::where('url', $this->url)->count());

        $this->command
            ->shouldReceive('confirm')
            ->once()
            ->with('/Are you sure you want stop monitoring/')
            ->andReturn('yes');

        Artisan::call('sites:delete', ['url' => $this->url]);

        $this->assertEquals(0, Site::where('url', $this->url)->count());
    }
}
