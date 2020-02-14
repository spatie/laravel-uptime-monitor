<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Notifications\Notification;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Monitor;

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
        return config('uptime-monitor.notifications.notifications.'.static::class);
    }

    public function getMonitor(): Monitor
    {
        return $this->event->monitor;
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $monitor = $this->getMonitor();

        $properties = array_merge([], $extraProperties);

        if ($monitor->certificate_check_enabled && $monitor->certificate_status === CertificateStatus::VALID) {
            $certificateTitle = "Certificate expires in {$monitor->formattedCertificateExpirationDate('forHumans')}";
            $certificateIssuer = $monitor->certificate_issuer;

            $properties[$certificateTitle] = $certificateIssuer;
        }

        return array_filter($properties);
    }

    public function getLocationDescription(): string
    {
        $configuredLocation = config('uptime-monitor.notifications.location');

        if ($configuredLocation == '') {
            return '';
        }

        return "Monitor {$configuredLocation}";
    }

    public function isStillRelevant(): bool
    {
        return true;
    }
}
