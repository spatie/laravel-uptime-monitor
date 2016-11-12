<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Services\PingMonitors\SiteCollection;

class SiteRepository
{
    public static function getAllForUptimeCheck(): SiteCollection
    {
        $sites = Site::query()
            ->get()
            ->filter(function (Site $site) {
                return $site->shouldCheckUptime();
            });

        return new SiteCollection($sites);
    }

    public static function getAllForSslCheck(): Collection
    {
        return Site::query()
            ->where('check_ssl_certificate', true)
            ->get();
    }

    public static function healthySites(): Collection
    {
        return Site::query()
            ->get()
            ->filter(function (Site $site) {
                return $site->isHealthy();
            });
    }

    public static function downSites()
    {
        return Site::query()
            ->where('uptime_status', UptimeStatus::DOWN)
            ->get();
    }

    public static function withSslProblems()
    {
        return Site::query()
            ->where('check_ssl_certificate', true)
            ->where('ssl_certificate_status', SslCertificateStatus::INVALID)
            ->get();
    }

    public static function unhealthySites(): Collection
    {
        return Site::query()
            ->get()
            ->reject(function (Site $site) {
                return $site->isHealthy();
            });
    }

    public static function uncheckedSites()
    {
        return Site::query()
            ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED)
            ->get();
    }

    protected static function query(): Builder
    {
        return Site::enabled()->orderBy('url');
    }
}
