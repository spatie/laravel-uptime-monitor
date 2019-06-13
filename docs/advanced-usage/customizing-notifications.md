---
title: Customizing notifications
weight: 4
---

This package leverages [Laravel's native notification capabilites](https://laravel.com/docs/5.4/notifications) to send out [several](https://docs.spatie.be/laravel-uptime-monitor/v3/monitoring-uptime/notifications) [notifications](https://docs.spatie.be/laravel-uptime-monitor/v3/monitoring-ssl-certificates/notifications).

```php
'notifications' => [
    \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckFailed::class => ['slack'],
    \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckRecovered::class => ['slack'],
    \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckSucceeded::class => [],

    \Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckFailed::class => ['slack'],
    \Spatie\UptimeMonitor\Notifications\Notifications\CertificateExpiresSoon::class => ['slack'],
    \Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckSucceeded::class => [],
],
```

Notice that the config keys are fully qualified class names of the `Notification` classes. All notifications have support for `slack` and `mail` out of the box. If you want to add support for more channels or just want to use change some text in the notifications you can specify your own notification classes in the config file. When creating custom notifications, it's probably best to extend the default ones shipped with this package.
