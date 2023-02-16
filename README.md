# A powerful, easy to configure uptime monitor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-uptime-monitor/run-tests?label=tests)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)

Laravel-uptime-monitor is a powerful, easy to configure uptime monitor. It will notify you when your site is down (and when it comes back up). You can also be notified a few days before an SSL certificate on one of your sites expires. Under the hood, the package leverages Laravel native notifications, so it's easy to use Slack, Telegram or your preferred notification provider.

You'll find extensive documentation on [https://spatie.be/docs/laravel-uptime-monitor]. It includes detailed info on how to install and use the package.

If you're not familiar with Laravel, but still want to use our uptime monitor, take a look at the [uptime-monitor-app](https://github.com/spatie/uptime-monitor-app) repo which contains a stand alone version of this package.

Reading the config file of this package is a good way to quickly get a feel of what `laravel-uptime-monitor` can do. Here's the content of the config file:

```php
return [

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail'
     * and 'slack'. Of course you can also specify your own notification classes.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckFailed::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckRecovered::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckSucceeded::class => [],

            \Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckFailed::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\CertificateExpiresSoon::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckSucceeded::class => [],
        ],

        /*
         * The location from where you are running this Laravel application. This location will be 
         * mentioned in all notifications that will be sent.
         */
        'location' => '',

        /*
         * To keep reminding you that a site is down, notifications
         * will be resent every given number of minutes.
         */
        'resend_uptime_check_failed_notification_every_minutes' => 60,

        'mail' => [
            'to' => ['your@email.com'],
        ],

        'slack' => [
            'webhook_url' => env('UPTIME_MONITOR_SLACK_WEBHOOK_URL'),
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => \Spatie\UptimeMonitor\Notifications\Notifiable::class,
        
        /**
         * The date format used in notifications.
         */
        'date_format' => 'd/m/Y',
    ],

    'uptime_check' => [

        /*
         * When the uptime check could reach the url of a monitor it will pass the response to this class
         * If this class determines the response is valid, the uptime check will be regarded as succeeded.
         *
         * You can use any implementation of Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers\UptimeResponseChecker here.
         */
        'response_checker' => Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers\LookForStringChecker::class,
            
        /*
         * An uptime check will be performed if the last check was performed more than the
         * given number of minutes ago. If you change this setting you have to manually
         * update the `uptime_check_interval_in_minutes` value of your existing monitors.
         *
         * When an uptime check fails we'll check the uptime for that monitor every time `monitor:check-uptime`
         * runs regardless of this setting.
         */
        'run_interval_in_minutes' => 5,

        /*
         * To speed up the uptime checking process the package can perform the uptime check of several
         * monitors concurrently. Set this to a lower value if you're getting weird errors
         * running the uptime check.
         */
        'concurrent_checks' => 10,

        /*
         * The uptime check for a monitor will fail if the url does not respond after the
         * given number of seconds.
         */
        'timeout_per_site' => 10,

        /*
         * Because networks can be a bit unreliable the package can make three attempts
         * to connect to a server in one uptime check. You can specify the time in
         * milliseconds between each attempt.
         */
        'retry_connection_after_milliseconds' => 100,

        /*
         * If you want to change the default Guzzle client behaviour, you can do so by
         * passing custom options that will be used when making requests.
         */
        'guzzle_options' => [
            // 'allow_redirects' => false,
        ],

        /*
         * Fire `Spatie\UptimeMonitor\Events\MonitorFailed` event only after
         * the given number of uptime checks have consecutively failed for a monitor.
         */
        'fire_monitor_failed_event_after_consecutive_failures' => 2,

        /*
         * When reaching out to sites this user agent will be used.
         */
        'user_agent' => 'spatie/laravel-uptime-monitor uptime checker',
        
        /*
         * When reaching out to the sites these headers will be added.
         */
        'additional_headers' => [],
    ],

    'certificate_check' => [

        /*
         * The `Spatie\UptimeMonitor\Events\SslExpiresSoon` event will fire
         * when a certificate is found whose expiration date is in
         * the next number of given days.
         */
        'fire_expiring_soon_event_if_certificate_expires_within_days' => 10,
    ],

    /*
     * To add or modify behaviour to the Monitor model you can specify your
     * own model here. The only requirement is that it should extend
     * `Spatie\UptimeMonitor\Models\Monitor`.
     */
    'monitor_model' => Spatie\UptimeMonitor\Models\Monitor::class,
];
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-uptime-monitor.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-uptime-monitor)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## API

Currently this package does not offer an API, if you need that take a look at [this package](https://github.com/LKDevelopment/laravel-uptime-monitor-api).

## Documentation
You'll find the documentation on [https://docs.spatie.be/laravel-uptime-monitor]. It includes detailed info on how to install and use the package.

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving the uptime monitor? Feel free to [create an issue on GitHub](https://github.com/spatie/laravel-uptime-monitor/issues), we'll try to address it as soon as possible.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

To run the tests, issue this command:

```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[https://docs.spatie.be/laravel-uptime-monitor]: https://docs.spatie.be/laravel-uptime-monitor/v3
