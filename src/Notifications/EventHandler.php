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

            $notification = $this->determineNotification($event);

            if (! $notification) {
                return;
            }

            if ($notification->isStillRelevant()) {
                $notifiable = $this->determineNotifiable();

                $notifiable->notify($notification);
            }

        });
    }

    protected function determineNotifiable()
    {
        $notifiableClass = $this->config->get('laravel-backup.notifications.notifiable');

        return app($notifiableClass);
    }

    protected function determineNotification($event)
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

        if ($notificationClass) {
            return app($notificationClass)->setEvent($event);
        }
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
