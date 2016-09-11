<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\ServiceProvider;
use Spatie\UptimeMonitor\Console\Commands\CheckUptimeMonitors;
use Spatie\UptimeMonitor\Console\Commands\CreateUptimeMonitor;
use Spatie\UptimeMonitor\Console\Commands\DeleteUptimeMonitor;
use Spatie\UptimeMonitor\Notifications\EventHandler;

class BackupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->publishes([
                __DIR__ . '/../config/laravel-backup.php' => config_path('laravel-backup.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-backup.php', 'laravel-backup');

        $this->app['events']->subscribe(EventHandler::class);

        $this->app->bind('command.check:run', CheckUptimeMonitors::class);
        $this->app->bind('command.create:clean', CreateUptimeMonitor::class);
        $this->app->bind('command.delete:list', DeleteUptimeMonitor::class);

        $this->commands([
            'command.uptime-monitor:run',
            'command.uptime-monitor:create',
            'command.uptime-monitor:delete'
        ]);

        $this->app->singleton(ConsoleOutput::class);
    }
}
