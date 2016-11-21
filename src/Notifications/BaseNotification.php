<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Notifications\Notification;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;

abstract class BaseNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('laravel-uptime-monitor.notifications.notifications.' . static::class);
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $monitor = $this->event->monitor;

        $properties = array_merge([], $extraProperties);

        if ($monitor->certificate_check_enabled && $monitor->certificate_status === CertificateStatus::VALID) {

            $certificateTitle = "{Certificate expires in} $monitor->formattedCertificateExpirationDate('forHumans')";
            $certificateIssuer = $monitor->certificate_issuer;

            $properties[$certificateTitle] = $certificateIssuer;
        }

        return array_filter($properties);
    }

    public function getLocationDescription(): string
    {
        $configuredLocation = config('laravel-uptime-monitor.notifications.location');

        if ($configuredLocation == '') {
            return '';
        }

        return " (says {$configuredLocation})";
    }

    public function isStillRelevant(): bool
    {
        return true;
    }
}
