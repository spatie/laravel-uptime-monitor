<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Services\PingMonitors\SiteCollection;

class SiteRepository
{
    public static function getAllForUptimeCheck(): SiteCollection
    {
        $sites = Site::enabled()
            ->get()
            ->filter(function (Site $site) {
                return $site->shouldCheckUptime();
            });

        return new SiteCollection($sites);
    }

    public static function getAllForSslCheck(): Collection
    {
        return Site::enabled()
            ->where('check_ssl_certificate', true)
            ->get();
    }
}