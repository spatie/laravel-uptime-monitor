<?php

use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;

$factory->define(Monitor::class, function (Faker\Generator $faker) {
    return [
        'url' => 'http://localhost:8080',
        'uptime_status' => UptimeStatus::UP,
        'uptime_check_interval_in_minutes' => config('laravel-uptime-monitor.uptime_check.run_interval_in_minutes'),
        'uptime_check_enabled' => true,
        'ssl_certificate_check_enabled' => false,
    ];
});
