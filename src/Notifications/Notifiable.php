<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return config('laravel-uptime-monitor.notifications.mail.to');
    }

    public function routeNotificationForSlack()
    {
        return config('laravel-uptime-monitor.notifications.slack.webhook_url');
    }

    public function getKey()
    {
        return static::class;
    }
}
