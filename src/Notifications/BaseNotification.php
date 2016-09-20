<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Notifications\Notification;
use Spatie\UptimeMonitor\Models\UptimeMonitor;

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

    public abstract function isStillRelevant(): bool;
}
