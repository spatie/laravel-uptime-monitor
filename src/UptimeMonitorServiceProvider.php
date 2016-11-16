<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Spatie\UptimeMonitor\Commands\CheckSslCertificates;
use Spatie\UptimeMonitor\Commands\CheckUptime;
use Spatie\UptimeMonitor\Commands\AddSite;
use Spatie\UptimeMonitor\Commands\DeleteSite;
use Spatie\UptimeMonitor\Commands\ListSites;
use Spatie\UptimeMonitor\Models\Site;
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

        $this->app->bind('command.sites:check-uptime', CheckUptime::class);
        $this->app->bind('command.sites:check-ssl', CheckSslCertificates::class);
        $this->app->bind('command.sites:add', AddSite::class);
        $this->app->bind('command.sites:delete', DeleteSite::class);
        $this->app->bind('command.sites:list', ListSites::class);

        $this->commands([
            'command.sites:check-uptime',
            'command.sites:check-ssl',
            'command.sites:add',
            'command.sites:delete',
            'command.sites:list',
        ]);

        Collection::macro('sortByHost', function () {
            return $this->sortBy(function (Site $site) {
                return $site->url->getHost();
            });
        });
    }
}
