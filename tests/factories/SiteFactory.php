<?php

use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;

$factory->define(Site::class, function (Faker\Generator $faker) {
    return [
        'url' => 'http://localhost:8080',
        'uptime_status' => UptimeStatus::UP,
        'enabled' => 1,
    ];
});
