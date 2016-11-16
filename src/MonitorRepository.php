<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Exceptions\InvalidConfiguration;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;

class MonitorRepository
{
    public static function getAllEnabledMonitors(): Collection
    {
        return self::query()
            ->get()
            ->sortByHost();
    }

    public static function getAllForUptimeCheck(): MonitorCollection
    {
        $monitors = self::query()
            ->get()
            ->filter(function (Monitor $monitor) {
                return $monitor->shouldCheckUptime();
            })
            ->sortByHost();

        return new MonitorCollection($monitors);
    }

    public static function getAllForSslCheck(): Collection
    {
        return self::query()
            ->where('check_ssl_certificate', true)
            ->get()
            ->sortByHost();
    }

    public static function healthyMonitors(): Collection
    {
        return self::query()
            ->get()
            ->filter(function (Monitor $monitor) {
                return $monitor->isHealthy();
            })
        ->sortByHost();
    }

    public static function getAllFailing(): Collection
    {
        return self::query()
            ->where('uptime_status', UptimeStatus::DOWN)
            ->get()
            ->sortByHost();
    }

    public static function getAllWithSslProblems(): Collection
    {
        return self::query()
            ->where('check_ssl_certificate', true)
            ->where('ssl_certificate_status', SslCertificateStatus::INVALID)
            ->get()
            ->sortByHost();
    }

    public static function getAllUnhealthy(): Collection
    {
        return self::query()
            ->get()
            ->reject(function (Monitor $monitor) {
                return $monitor->isHealthy();
            })
            ->sortByHost();
    }

    public static function getAllUnchecked(): Collection
    {
        return self::query()
            ->where('uptime_status', UptimeStatus::NOT_YET_CHECKED)
            ->get()
            ->sortByHost();
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
