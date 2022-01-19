<?php

namespace Spatie\UptimeMonitor\Test;

use Artisan;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Notifications\SlackChannelServiceProvider;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\UptimeMonitor\UptimeMonitorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected Server $server;

    public function setUp(): void
    {
        $this->server = new Server(new Client());

        Carbon::setTestNow(Carbon::create(2016, 1, 1, 00, 00, 00));

        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\UptimeMonitor\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SlackChannelServiceProvider::class,
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
        include_once __DIR__.'/../database/migrations/create_monitors_table.php.stub';

        (new \CreateMonitorsTable())->up();
    }

    public function progressMinutes(int $minutes)
    {
        $newNow = Carbon::now()->addMinutes($minutes);

        Carbon::setTestNow($newNow);
    }

    public function bringTestServerUp()
    {
        $this->server->up();
    }

    public function bringTestServerDown()
    {
        $this->server->down();
    }

    /**
     * @param string|array $searchStrings
     */
    protected function seeInConsoleOutput($searchStrings)
    {
        if (! is_array($searchStrings)) {
            $searchStrings = [$searchStrings];
        }

        $output = Artisan::output();

        foreach ($searchStrings as $searchString) {
            $this->assertStringContainsString((string) $searchString, $output);
        }
    }

    /**
     * @param string|array $searchStrings
     */
    protected function dontSeeInConsoleOutput($searchStrings)
    {
        if (! is_array($searchStrings)) {
            $searchStrings = [$searchStrings];
        }

        $output = Artisan::output();

        foreach ($searchStrings as $searchString) {
            $this->assertStringNotContainsString((string) $searchString, $output);
        }
    }

    public function skipIfNotConnectedToTheInternet()
    {
        try {
            file_get_contents('https://google.com');
        } catch (\ErrorException $e) {
            $this->markTestSkipped('No internet connection available.');
        }
    }

    protected function resetEventAssertions()
    {
        Event::fake();
    }
}
