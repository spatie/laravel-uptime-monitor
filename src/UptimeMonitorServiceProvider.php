<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\ServiceProvider;
use Spatie\UptimeMonitor\Commands\CheckSslCertificates;
use Spatie\UptimeMonitor\Commands\CheckUptime;
use Spatie\UptimeMonitor\Commands\CreateSite;
use Spatie\UptimeMonitor\Commands\DeleteSite;
use Spatie\UptimeMonitor\Commands\ListSites;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Notifications\EventHandler;

class UptimeMonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../config/laravel-uptime-monitor.php' => config_path('laravel-uptime-monitor.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-uptime-monitor.php', 'laravel-uptime-monitor');

        $this->app['events']->subscribe(EventHandler::class);

        $this->app->bind('command.sites:check-uptime', CheckUptime::class);
        $this->app->bind('command.sites:check-ssl', CheckSslCertificates::class);
        $this->app->bind('command.sites:create', CreateSite::class);
        $this->app->bind('command.sites:delete', DeleteSite::class);
        $this->app->bind('command.sites:list', ListSites::class);

        $this->commands([
            'command.sites:check-uptime',
            'command.sites:check-ssl',
            'command.sites:create',
            'command.sites:delete',
            'command.sites:list',
        ]);

        $this->app->singleton(ConsoleOutput::class);
    }
}
