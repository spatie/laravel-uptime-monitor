<?php
namespace Spatie\UptimeMonitor\Test\Commands;

use Mockery as  m;
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
    }

    /**
     * @test
     */
    public function it_tests()
    {
        $this->command->shouldReceive('ask')->once()->with('/Which url to you want to monitor/')->andReturn('https://mysite.com');

        $this->command->shouldReceive('confirm')->once()->with('/Should we look for a specific string on the response/')->andReturn('');


        $this->app->bind('command.sites:create', function() { return $this->command; });
        //$this->app['artisan']->add($this->command);
        \Artisan::call('sites:create');

        $this->assertTrue($);


    }




}


