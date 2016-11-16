<?php

namespace Spatie\UptimeMonitor\Notifications;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound;
use Spatie\UptimeMonitor\Events\MonitorRecovered;
use Spatie\UptimeMonitor\Events\MonitorHealthy;
use Spatie\UptimeMonitor\Events\MonitorFailed;
use Spatie\UptimeMonitor\Events\SoonExpiringSslCertificateFound;
use Spatie\UptimeMonitor\Events\ValidSslCertificateFound;

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
        $events->listen($this->allEventClasses(), function ($event) {
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
        $notifiableClass = $this->config->get('laravel-uptime-monitor.notifications.notifiable');

        return app($notifiableClass);
    }

    protected function determineNotification($event)
    {
        $eventName = class_basename($event);

        $notificationClass = collect($this->config->get('laravel-uptime-monitor.notifications.notifications'))
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

    protected function allEventClasses(): array
    {
        return [
            MonitorFailed::class,
            MonitorHealthy::class,
            MonitorRecovered::class,
            ValidSslCertificateFound::class,
            InvalidSslCertificateFound::class,
            SoonExpiringSslCertificateFound::class,
        ];
    }
}
