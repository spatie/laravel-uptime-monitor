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

        $properties['url'] = $site->url;

        if (! empty($site->look_for_string)) {
            $properties['look for string'] = $site->look_for_string;
        }

        $properties = array_merge($properties, $extraProperties);

        if ($site->check_ssl_certificate) {
            $properties['ssl certificate valid'] = $site->ssl_certificate_valid ? 'yes' : 'no';
            $properties['ssl certificate expiration date'] = $properties->ssl_certificate_expiration_date->format('Y/m/d H:i:s');
        }

        return $properties;
    }

    public abstract function isStillRelevant(): bool;
}
