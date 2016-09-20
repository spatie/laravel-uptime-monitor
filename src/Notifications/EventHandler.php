<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteDown;

class EventHandler
{
    /** @var \Illuminate\Config\Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen($this->allUptimeMonitorEventClasses(), function ($event) {
            $notifiable = $this->determineNotifiable();

            $notification = $this->determineNotification($event);

            if ($notification->isStillRelevant()) {
                $notifiable->notify($notification);
            }

        });
    }

    protected function determineNotifiable()
    {
        $notifiableClass = $this->config->get('laravel-backup.notifications.notifiable');

        return app($notifiableClass);
    }

    protected function determineNotification($event): Notification
    {
        $eventName = class_basename($event);

        $notificationClass = collect($this->config->get('laravel-backup.notifications.notifications'))
            ->filter(function (array $notificationChannels) {
                return count($notificationChannels);
            })
            ->keys()
            ->first(function ($notificationClass) use ($eventName) {
                $notificationName = class_basename($notificationClass);

                return $notificationName === $eventName;
            });

        if (!$notificationClass) {
            throw NotificationCouldNotBeSent::noNotifcationClassForEvent($event);
        }

        return app($notificationClass)->setEvent($event);
    }

    protected function allUptimeMonitorEventClasses(): array
    {
        return [
            SiteDown::class,
            SiteUp::class,
            SiteRestored::class,
        ];
    }
}
