<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Exceptions\InvalidConfiguration;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;

class SiteRepository
{
    public static function getAllEnabledSites(): Collection
    {
        return self::query()
            ->get()
            ->sortByHost();
    }

    public static function getAllForUptimeCheck(): SiteCollection
    {
        $sites = self::query()
            ->get()
            ->filter(function (Site $site) {
                return $site->shouldCheckUptime();
            })
            ->sortByHost();

        return new SiteCollection($sites);
    }

    public static function getAllForSslCheck(): Collection
    {
        return self::query()
            ->where('check_ssl_certificate', true)
            ->get()
            ->sortByHost();
    }

    public static function healthySites(): Collection
    {
        return self::query()
            ->get()
            ->filter(function (Site $site) {
                return $site->isHealthy();
            })
        ->sortByHost();
    }

    public static function downSites(): Collection
    {
        return self::query()
            ->where('uptime_status', UptimeStatus::DOWN)
            ->get()
            ->sortByHost();
    }

    public static function withSslProblems(): Collection
    {
        return self::query()
            ->where('check_ssl_certificate', true)
            ->where('ssl_certificate_status', SslCertificateStatus::INVALID)
            ->get()
            ->sortByHost();
    }

    public static function unhealthySites(): Collection
    {
        return self::query()
            ->get()
            ->reject(function (Site $site) {
                return $site->isHealthy();
            })
            ->sortByHost();
    }

    public static function uncheckedSites(): Collection
    {
        return self::query()
            ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED)
            ->get()
            ->sortByHost();
    }

    protected static function query()
    {
        $modelClass = static::determineSiteModel();

        return $modelClass::enabled();
    }

    protected static function determineSiteModel(): string
    {
        $siteModel = config('laravel-uptime-monitor.site_model') ?? Site::class;

        if (! is_a($siteModel, Site::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($siteModel);
        }

        return $siteModel;
    }
}
