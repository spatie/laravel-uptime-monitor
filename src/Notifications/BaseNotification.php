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

    public function getUptimeMonitorProperties($extraProperties): array
    {
        $site = $this->event->uptimeMonitor;

        $properties['url'] = (string) $site->url;

        if (! empty($site->look_for_string)) {
            $properties['look for string'] = $site->look_for_string;
        }

        $properties = array_merge($properties, $extraProperties);

        if ($site->check_ssl_certificate) {
            $properties['ssl certificate valid'] = $site->ssl_certificate_status;
            $properties['ssl certificate expiration date'] = $site->ssl_certificate_expiration_date->diffForHumans();
        }

        return $properties;
    }

    abstract public function isStillRelevant(): bool;
}
