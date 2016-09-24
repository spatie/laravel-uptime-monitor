<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\ServiceProvider;
use Spatie\UptimeMonitor\Commands\CheckUptimeMonitors;
use Spatie\UptimeMonitor\Commands\CreateUptimeMonitor;
use Spatie\UptimeMonitor\Commands\DeleteUptimeMonitor;
use Spatie\UptimeMonitor\Notifications\EventHandler;

class UptimeMonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->publishes([
                __DIR__ . '/../config/laravel-uptime-monitor.php' => config_path('laravel-uptime-monitor.php'),
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

        $this->app->bind('command.uptime-monitor:check', CheckUptimeMonitors::class);
        $this->app->bind('command.uptime-monitor:create', CreateUptimeMonitor::class);
        $this->app->bind('command.uptime-monitor:delete', DeleteUptimeMonitor::class);

        $this->commands([
            'command.uptime-monitor:check',
            'command.uptime-monitor:create',
            'command.uptime-monitor:delete'
        ]);
    }
}
