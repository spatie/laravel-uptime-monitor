<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('laravel-uptime-monitor.notifications.notifications.'.static::class);
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $monitor = $this->event->monitor;

        $properties['Location'] = config('laravel-uptime-monitor.notifications.location');

        $properties['Url'] = (string) $monitor->url;

        if (! empty($monitor->look_for_string)) {
            $properties['Look for string'] = $monitor->look_for_string;
        }

        $properties = array_merge($properties, $extraProperties);

        if ($monitor->certificate_check_enabled) {
            $properties['Ssl certificate status'] = $monitor->certificate_status;
            $properties['Ssl certificate issuer'] = $monitor->certificate_issuer;
            $properties['Ssl certificate expiration date'] = $monitor->formattedSslCertificateExpirationDate;
        }

        return array_filter($properties);
    }

    public function isStillRelevant(): bool
    {
        return true;
    }
}
