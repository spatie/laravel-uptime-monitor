<?php

return [

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail' and 'slack'.
     * For Slack you need to install guzzlehttp/guzzle.
     *
     * You can also use your own notification classes, just make sure the class is named after one of
     * the `Spatie\Backup\Events` classes.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\UptimeMonitor\Notifications\Notifications\SiteDown::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SiteRestored::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SiteUp::class => ['slack'],

            \Spatie\UptimeMonitor\Notifications\Notifications\InvalidSslCertificateFound::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\SoonExpiringSslCertificateFound::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\ValidSslCertificateFound::class => ['slack'],
        ],

        /**
         * Fire SiteDown-event only after the given amount of checks have consecutively failed.
         */
        'fire_down_event_after_consecutive_failed_checks' => 1,

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

    /*
     * The location from where you are running the uptime checks. This location will be mentioned
     * in all notifications that will be sent
     */
    'location' => '',


    /**
     * To speed up the uptime checking process uptime monitor can check multiple sites
     * concurrently. Set this to a lower value if you're getting weird errors
     * running the uptime check.
     */
    'concurrent_uptime_checks' => 10,
];
