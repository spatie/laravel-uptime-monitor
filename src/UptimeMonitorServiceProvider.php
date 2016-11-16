<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Spatie\UptimeMonitor\Commands\CheckSslCertificates;
use Spatie\UptimeMonitor\Commands\CheckUptime;
use Spatie\UptimeMonitor\Commands\CreateMonitor;
use Spatie\UptimeMonitor\Commands\DeleteMonitor;
use Spatie\UptimeMonitor\Commands\ListMonitors;
use Spatie\UptimeMonitor\Models\Monitor;
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

        if (! class_exists('CreateSitesTable')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_monitors_table.php.stub' => database_path('migrations/'.$timestamp.'_create_monitors_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-uptime-monitor.php', 'laravel-uptime-monitor');

        $this->app['events']->subscribe(EventHandler::class);

        $this->app->bind('command.monitor:check-uptime', CheckUptime::class);
        $this->app->bind('command.monitor:check-ssl', CheckSslCertificates::class);
        $this->app->bind('command.monitor:create', CreateMonitor::class);
        $this->app->bind('command.monitor:delete', DeleteMonitor::class);
        $this->app->bind('command.monitor:list', ListMonitors::class);

        $this->commands([
            'command.monitor:check-uptime',
            'command.monitor:check-ssl',
            'command.monitor:create',
            'command.monitor:delete',
            'command.monitor:list',
        ]);

        Collection::macro('sortByHost', function () {
            return $this->sortBy(function (Monitor $monitor) {
                return $monitor->url->getHost();
            });
        });
    }
}
