<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\ServiceProvider;

class UptimeMonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/UptimeMonitor.php' => config_path('UptimeMonitor.php'),
        ], 'config');

        /*
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'UptimeMonitor');

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/UptimeMonitor'),
        ], 'views');
        */
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'UptimeMonitor');
    }
}
