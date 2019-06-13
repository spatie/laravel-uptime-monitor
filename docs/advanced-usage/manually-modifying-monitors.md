---
title: Manually modifying monitors
weight: 1
---

All configured monitors are stored in the `monitors` table in the database. The various `monitor` commands manipulate the data that table:

 - `monitor:create` adds a row
 - `monitor:delete` deletes a row
 - `monitor:enable` and `monitor:disable` change the value of the `enabled` field
 - `monitor:list` lists all rows
 - `monitor:sync-file` syncs monitors from a json file (see [syncing monitors from a file](https://docs.spatie.be/laravel-uptime-monitor/v3/advanced-usage/syncing-monitors-from-a-file))

You can also manually manipulate the table rows instead. Here's a description of the fields you can manipulate:

 - `url`: the url to perform uptime and ssl certificate checks on. Take care not to insert duplicate values.
 - `uptime_check_enabled`: determines if the uptime check should be performed for this monitor.
 - `certificate_check_enabled`: determines if the ssl certificate check should be performed for this monitor.
 - `look_for_string`: if this string is not found in the response the uptime check will fail. You may set this to an empty string to disable the check.
 - `uptime_check_interval_in_minutes`: if the uptime check was successful that site won't be checked again for at least this number of minutes. When a monitor is created this field is filled with the value of `uptime_check_interval_in_minutes` in the config file.
 - `uptime_check_method`: the `http` method used by the uptime check. If `look_for_string` is specified when creating the monitor this will be set to `get`, otherwise this will be `head`.
 - `uptime_check_payload`: a payload that will be sent as the monitor request body. If you are using this field, you should set the `Content-Type` header in the `uptime_check_additional_headers` field.
 - `uptime_check_additional_headers`: additional headers that are sent in the request. The value shoule be escaped `JSON`. It will be decoded using `json_decode`. Example: `{"Content-Type":"application\/json"}`
 - `uptime_check_response_checker`: the fully qualified class name of a custom response checker that will be used only for this monitor. It must be an implementation of `Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers\UptimeResponseChecker` and will be resolved using the [service container](https://laravel.com/docs/5.5/container).

 All other fields in the `monitors` table are managed by the package and should not be manually modified.
