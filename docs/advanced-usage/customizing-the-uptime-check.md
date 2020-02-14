---
title: Customizing the uptime check
weight: 3
---

This package ships with a default configured uptime check. You can modify the behaviour of the uptime check by changing the values under the `uptime_check` key in the config file.

These are the default values:

```php
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
          * When an uptime check fails we'll check the uptime for that montitor every time `monitor:check-uptime`
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
          * The uptime check for a monitor will fail if url does not respond after the
          * given number of seconds.
          */
         'timeout_per_site' => 10,
 
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
```
