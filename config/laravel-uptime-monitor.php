<?php

return [

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail'
     * and 'slack'. Of course you can also specify your own notification classes.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\UptimeMonitor\Notifications\Notifications\SiteDown::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SiteRestored::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SiteUp::class => [],

            \Spatie\UptimeMonitor\Notifications\Notifications\InvalidSslCertificateFound::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SoonExpiringSslCertificateFound::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\ValidSslCertificateFound::class => [],
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

        /*
         * You will be notified whenever an ssl certificate will
         * expire in the given amount of days.
         */
        'send_notification_when_ssl_certificate_will_expire_in_days' => 10,

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
         * Fire SiteDown-event only after the given amount of checks
         * have consecutively failed for a site.
         */
        'fire_down_event_after_consecutive_failures' => 2,

        /*
         * When reaching out to sites this user agent will be used.
         */
        'user_agent' => 'spatie/laravel-uptime-monitor uptime checker',
    ],

    /*
     * To add or modify behaviour to the Site model you can specify your
     * own model here. They only requirement is that it should extend
     * `Spatie\UptimeMonitor\Test\Models\Site`.
     */
     'site_model' => Spatie\UptimeMonitor\Models\Site::class,
];
