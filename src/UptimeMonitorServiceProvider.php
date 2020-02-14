<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\ServiceProvider;
use Spatie\UptimeMonitor\Commands\CheckCertificates;
use Spatie\UptimeMonitor\Commands\CheckUptime;
use Spatie\UptimeMonitor\Commands\CreateMonitor;
use Spatie\UptimeMonitor\Commands\DeleteMonitor;
use Spatie\UptimeMonitor\Commands\DisableMonitor;
use Spatie\UptimeMonitor\Commands\EnableMonitor;
use Spatie\UptimeMonitor\Commands\ListMonitors;
use Spatie\UptimeMonitor\Commands\SyncFile;
use Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers\UptimeResponseChecker;
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
                __DIR__.'/../config/uptime-monitor.php' => config_path('uptime-monitor.php'),
            ], 'config');
        }

        if (! class_exists('CreateMonitorsTable')) {
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
        $this->mergeConfigFrom(__DIR__.'/../config/uptime-monitor.php', 'uptime-monitor');

        $this->app['events']->subscribe(EventHandler::class);

        $this->app->bind('command.monitor:check-uptime', CheckUptime::class);
        $this->app->bind('command.monitor:check-certificate', CheckCertificates::class);
        $this->app->bind('command.monitor:sync-file', SyncFile::class);
        $this->app->bind('command.monitor:create', CreateMonitor::class);
        $this->app->bind('command.monitor:delete', DeleteMonitor::class);
        $this->app->bind('command.monitor:enable', EnableMonitor::class);
        $this->app->bind('command.monitor:disable', DisableMonitor::class);
        $this->app->bind('command.monitor:list', ListMonitors::class);

        $this->app->bind(
            UptimeResponseChecker::class,
            config('uptime-monitor.uptime_check.response_checker')
        );

        $this->commands([
            'command.monitor:check-uptime',
            'command.monitor:check-certificate',
            'command.monitor:sync-file',
            'command.monitor:create',
            'command.monitor:delete',
            'command.monitor:enable',
            'command.monitor:disable',
            'command.monitor:list',
        ]);
    }
}
