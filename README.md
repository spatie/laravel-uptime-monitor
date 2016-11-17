# DO NOT USE (yet)

This code is not yet stable. Brave souls may try to use, but be awere there might be bugs (and breaking changes until version 1.0.0 is tagged).

# A powerful, easy to configure uptime monitor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-uptime-monitor/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-uptime-monitor)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/2551eaa3-34df-49e0-a170-709b96f2ac3e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/2551eaa3-34df-49e0-a170-709b96f2ac3e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-uptime-monitor)
[![StyleCI](https://styleci.io/repos/67774357/shield?branch=master)](https://styleci.io/repos/67774357)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)

Laravel-uptime-monitor is a powerful, easy to configure uptime monitor. It will notify you when your site is down (and when it comes back up). You can also be notified a few days before an SSL certificate on one of your sites expires. Under the hood, the package leverages Laravel 5.3's notifications, so it's easy to use Slack, Telegram or your preferred notification provider.

You'll find extensive documentation on https://docs.spatie.be/laravel-uptime-monitor/v1. It includes detailed info on how to install and use the package.

Reading the config file of this package is a good way to quickly get a feel of what `laravel-uptime-monitor` can do. Here's the content of the config file:

```php
return [

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail'
     * and 'slack'. Of course you can also specify your own notification classes.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\UptimeMonitor\Notifications\Notifications\MonitorFailed::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\MonitorRecovered::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\MonitorSucceeded::class => [],

            \Spatie\UptimeMonitor\Notifications\Notifications\SslCheckFailed::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SslExpiresSoon::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SslCheckSucceeded::class => [],
        ],

        /*
         * The location from where you are running this Laravel application. This location will be mentioned
         * in all notifications that will be sent.
         */
        'location' => '',

        /*
         * To keep reminding you that a site is down down notifications
         * will be resent every given amount of minutes.
         */
        'resend_down_notification_every_minutes' => 60,

        'mail' => [
            'to' => 'your@email.com',
        ],

        'slack' => [
            'webhook_url' => env('UPTIME_MONITOR_SLACK_WEBHOOK_URL'),
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => \Spatie\UptimeMonitor\Notifications\Notifiable::class,
    ],

    'uptime_check' => [

        /*
         * An uptime check will be performed if the last check was performed more that the
         * given amount of minutes ago. If you change this setting you have to manually
         * update the `uptime_check_interval_in_minutes` value of your existing sites.
         *
         * When a site is down we'll check the uptime every time `sites:check-uptime` runs
         * regardless of this setting.
         */
        'run_interval_in_minutes' => 5,

        /*
         * To speed up the uptime checking process uptime monitor can check multiple sites
         * concurrently. Set this to a lower value if you're getting weird errors
         * running the uptime check.
         */
        'concurrent_checks' => 10,

        /*
         * The uptime check for a site will fail if site does not respond after the
         * given amount of seconds.
         */
        'timeout_per_site' => 10,

        /*
         * Fire `Spatie\UptimeMonitor\Events\MonitorFailed` event only after
         * the given amount of checks have consecutively failed for a site.
         */
        'fire_monitor_failed_event_after_consecutive_failures' => 2,

        /*
         * When reaching out to sites this user agent will be used.
         */
        'user_agent' => 'spatie/laravel-uptime-monitor uptime checker',
    ],

    'ssl-check' => [

        /*
         * The `Spatie\UptimeMonitor\Events\SslExpiresSoon` event will fire
         * when a certificate is found whose expiration date is in
         * the next amount given days.
         */
        'fire_expiring_soon_event_if_certificate_expires_within_days' => 10,
    ],

    /*
     * To add or modify behaviour to the Site model you can specify your
     * own model here. They only requirement is that it should extend
     * `Spatie\UptimeMonitor\Test\Models\Site`.
     */
     'monitor_model' => Spatie\UptimeMonitor\Models\Monitor::class,
];
```

## Documentation
You'll find the documentation on [https://docs.spatie.be/laravel-uptime-monitor/v1](https://docs.spatie.be/laravel-uptime-monitor/v1). It includes detailed info on how to install and use the package.

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving the media library? Feel free to [create an issue on GitHub](https://github.com/spatie/laravel-uptime-monitor/issues), we'll try to address it as soon as possible.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

To run the tests you'll have to start the included node based server first

``` bash
cd tests/server
./start_server.sh

cd ../..
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
