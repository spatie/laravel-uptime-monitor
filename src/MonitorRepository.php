<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Monitor;
use Illuminate\Database\Eloquent\Builder;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Exceptions\InvalidConfiguration;

class MonitorRepository
{
    public static function getEnabled(): Collection
    {
        $monitors = self::query()->get();

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getDisabled(): Collection
    {
        $modelClass = static::determineMonitorModel();

        $monitors = $modelClass::where('uptime_check_enabled', false)
            ->where('certificate_check_enabled', false)
            ->get();

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getForUptimeCheck(): MonitorCollection
    {
        $monitors = self::query()
            ->get()
            ->filter(function (Monitor $monitor) {
                return $monitor->shouldCheckUptime();
            });

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getForCertificateCheck(): Collection
    {
        $monitors = self::query()
            ->where('certificate_check_enabled', true)
            ->get();

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getHealthy(): Collection
    {
        $monitors = self::query()
            ->get()
            ->filter(function (Monitor $monitor) {
                return $monitor->isHealthy();
            });

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getWithFailingUptimeCheck(): Collection
    {
        $monitors = self::query()
            ->where('uptime_check_enabled', true)
            ->where('uptime_status', UptimeStatus::DOWN)
            ->get();

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getWithFailingCertificateCheck(): Collection
    {
        $monitors = self::query()
            ->where('certificate_check_enabled', true)
            ->where('certificate_status', CertificateStatus::INVALID)
            ->get();

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getUnhealthy(): Collection
    {
        $monitors = self::query()
            ->get()
            ->reject(function (Monitor $monitor) {
                return $monitor->isHealthy();
            });

        return MonitorCollection::make($monitors)->sortByHost();
    }

    public static function getUnchecked(): Collection
    {
        $modelClass = static::determineMonitorModel();

        $monitors = $modelClass
            ::where(function (Builder $query) {
                $query
                    ->where('uptime_check_enabled', true)
                    ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED);
            })
            ->orWhere(function (Builder $query) {
                $query
                    ->where('uptime_check_enabled', true)
                    ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED);
            })
            ->get();

        return MonitorCollection::make($monitors)->sortByHost();
    }

    /**
     * @param string|\Spatie\Url\Url $url
     *
     * @return \Spatie\UptimeMonitor\Models\Monitor
     */
    public static function findByUrl($url)
    {
        $model = static::determineMonitorModel();

        return $model::where('url', (string) $url)->first();
    }

    protected static function query()
    {
        $modelClass = static::determineMonitorModel();

        return $modelClass::enabled();
    }

    protected static function determineMonitorModel(): string
    {
        $monitorModel = config('laravel-uptime-monitor.monitor_model') ?? Monitor::class;

        if (! is_a($monitorModel, Monitor::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($monitorModel);
        }

        return $monitorModel;
    }
}
