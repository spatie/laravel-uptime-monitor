---
title: Events
weight: 2
---

These events are fired by the uptime check of a monitor.

## UptimeCheckFailed

`Spatie\UptimeMonitor\Events\UptimeCheckFailed`

This event is fired when the uptime check of the monitor has consecutively failed a couple of times. The specific number of failures can be configured in the `fire_monitor_failed_event_after_consecutive_failures` key in the config file. This happens when the configured `url` could not be reached or, if you specified it, the `look_for_string` value could not be found in the response. 

It has one public property, `$monitor`, that contains an instance of `Spatie\UptimeMonitor\Models\Monitor`.

## UptimeCheckRecovered

`Spatie\UptimeMonitor\Events\UptimeCheckRecovered`

This event is fired after the uptime check is successful after it has failed.

It has one public property, `$monitor`, that contains an instance of `Spatie\UptimeMonitor\Models\Monitor`.

## UptimeCheckSucceeded

`Spatie\UptimeMonitor\Events\UptimeCheckSucceeded`

This event is fired when the monitor could reach the configured `url` and, if you specified it, found the `look_for_string` value in the response. This event only takes the uptime check into consideration, so it will still be fired if the ssl certificate check of the monitor is failing.

It has one public property, `$monitor`, that contains an instance of `Spatie\UptimeMonitor\Models\Monitor`.
