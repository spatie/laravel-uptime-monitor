<?php

namespace Spatie\UptimeMonitor\Test;

use Carbon\Carbon;
use Event;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\UptimeMonitor\UptimeMonitorServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        Carbon::setTestNow(Carbon::create(2016, 1, 1, 00, 00, 00));

        parent::setUp();

        $this->withFactories(__DIR__.'/factories');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            UptimeMonitorServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('mail.driver', 'log');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'prefix' => '',
            'database' => ':memory:',
        ]);

        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {

        include_once __DIR__.'/../database/migrations/create_sites_table.php.stub';

        (new \CreateSitesTable())->up();


    }
}
