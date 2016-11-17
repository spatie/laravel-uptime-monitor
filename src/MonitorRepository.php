<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Exceptions\InvalidConfiguration;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;

class MonitorRepository
{
    public static function getEnabled(): Collection
    {
        return self::query()
            ->get()
            ->sortByHost();
    }

    public static function getDisabled(): Collection
    {
        $modelClass = static::determineMonitorModel();

        return $modelClass::where('uptime_check_enabled', false)
            ->where('certificate_check_enabled', false)
            ->get();
    }

    public static function getForUptimeCheck(): MonitorCollection
    {
        $monitors = self::query()
            ->get()
            ->filter(function (Monitor $monitor) {
                return $monitor->shouldCheckUptime();
            })
            ->sortByHost();

        return new MonitorCollection($monitors);
    }

    public static function getForCertificateCheck(): Collection
    {
        return self::query()
            ->where('certificate_check_enabled', true)
            ->get()
            ->sortByHost();
    }

    public static function getHealthy(): Collection
    {
        return self::query()
            ->get()
            ->filter(function (Monitor $monitor) {
                return $monitor->isHealthy();
            })
        ->sortByHost();
    }

    public static function getWithFailingUptimeCheck(): Collection
    {
        return self::query()
            ->where('uptime_check_enabled', true)
            ->where('uptime_status', UptimeStatus::DOWN)
            ->get()
            ->sortByHost();
    }

    public static function getWithFailingCertificateCheck(): Collection
    {
        return self::query()
            ->where('certificate_check_enabled', true)
            ->where('certificate_status', SslCertificateStatus::INVALID)
            ->get()
            ->sortByHost();
    }

    public static function getUnhealthy(): Collection
    {
        return self::query()
            ->get()
            ->reject(function (Monitor $monitor) {
                return $monitor->isHealthy();
            })
            ->sortByHost();
    }

    public static function getUnchecked(): Collection
    {
        return self::query()
            ->whereColumn([
                ['uptime_check_enabled', '=', true],
                ['uptime_status', '=', UptimeStatus::NOT_YET_CHECKED]
            ])
            ->orWhereColumn([
                ['certificate_check_enabled', '=', true],
                ['certificate_status', '=', SslCertificateStatus::NOT_YET_CHECKED]
            ])
            ->get()
            ->sortByHost();
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
