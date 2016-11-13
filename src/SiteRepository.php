<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
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
            })
            ->sortByHost();

        return new SiteCollection($sites);
    }

    public static function getAllForSslCheck(): Collection
    {
        return Site::enabled()
            ->where('check_ssl_certificate', true)
            ->get()
            ->sortByHost();
    }

    public static function healthySites(): Collection
    {
        return Site::enabled()
            ->get()
            ->filter(function (Site $site) {
                return $site->isHealthy();
            })
        ->sortByHost();

    }

    public static function downSites()
    {
        return Site::enabled()
            ->where('uptime_status', UptimeStatus::DOWN)
            ->get()
            ->sortByHost();;
    }

    public static function withSslProblems()
    {
        return Site::enabled()
            ->where('check_ssl_certificate', true)
            ->where('ssl_certificate_status', SslCertificateStatus::INVALID)
            ->get()
            ->sortByHost();;
    }

    public static function unhealthySites(): Collection
    {
        return Site::enabled()
            ->get()
            ->reject(function (Site $site) {
                return $site->isHealthy();
            })
            ->sortByHost();
    }

    public static function uncheckedSites()
    {
        return Site::enabled()
            ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED)
            ->get()
            ->sortByHost();
    }
}
