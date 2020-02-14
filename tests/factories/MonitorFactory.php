<?php

use Carbon\Carbon;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;

$factory->define(Monitor::class, function (Faker\Generator $faker) {
    return [
        'url' => sprintf('http://localhost:%d', getenv('TEST_SERVER_PORT')),
        'uptime_status' => UptimeStatus::UP,
        'uptime_check_interval_in_minutes' => config('uptime-monitor.uptime_check.run_interval_in_minutes'),
        'uptime_status_last_change_date' => Carbon::now(),
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ];
});
