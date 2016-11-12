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
            ->orderBy('url')
            ->get()
            ->filter(function (Site $site) {
                return $site->shouldCheckUptime();
            });

        return new SiteCollection($sites);
    }

    public static function getAllForSslCheck(): Collection
    {
        return Site::enabled()
            ->orderBy('url')
            ->where('check_ssl_certificate', true)
            ->get();
    }

    public static function healthySites(): Collection
    {
        return Site::enabled()
            ->orderBy('url')
            ->get()
            ->filter(function (Site $site) {
                return $site->isHealthy();
            });
    }

    public static function downSites()
    {
        return Site::enabled()
            ->orderBy('url')
            ->get()
            ->filter(function (Site $site) {
                return $site->uptime_status == UptimeStatus::DOWN;
            });
    }

    public static function withSslProblems()
    {
        return Site::enabled()
            ->orderBy('url')
            ->get()
            ->filter(function (Site $site) {
                return $site->ssl_certificate_status == SslCertificateStatus::INVALID;
            });
    }

    public static function unhealthySites(): Collection
    {
        return Site::enabled()
            ->orderBy('url')
            ->get()
            ->reject(function (Site $site) {
                return $site->isHealthy();
            });
    }

    public static function uncheckedSites()
    {
        return Site::enabled()
            ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED)
            ->orderBy('url')
            ->get();
    }
}
