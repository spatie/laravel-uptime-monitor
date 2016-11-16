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

    public function getSiteProperties($extraProperties = []): array
    {
        $monitor = $this->event->site;

        $properties['location'] = config('laravel-uptime-monitor.notifications.location');

        $properties['url'] = (string) $monitor->url;

        if (! empty($monitor->look_for_string)) {
            $properties['look for string'] = $monitor->look_for_string;
        }

        $properties = array_merge($properties, $extraProperties);

        if ($monitor->check_ssl_certificate) {
            $properties['ssl certificate valid'] = $monitor->ssl_certificate_status;
            $properties['ssl certificate issuer'] = $monitor->ssl_certificate_issuer;
            $properties['ssl certificate expiration date'] = $monitor->formattedSslCertificateExpirationDate;
        }

        return array_filter($properties);
    }

    public function isStillRelevant(): bool
    {
        return true;
    }
}
