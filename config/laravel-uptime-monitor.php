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
            \Spatie\UptimeMontior\Notifications\Notifications\BackupHasFailed::class         => ['mail'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => \Spatie\UptimeMontior\Notifications\Notifiable::class,

        'mail' => [
            'to' => 'your@email.com',
        ],

        'slack' => [
            'webhook_url' => '',
        ],
    ],
];
