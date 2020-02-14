---
title: Adding and removing sites
weight: 1
---

## Creating your first monitor

After you've set up [the package](https://docs.spatie.be/laravel-uptime-monitor/v3/installation-and-setup) you can use the `monitor:create` [artisan](https://laravel.com/docs/5.4/artisan) command to monitor a url. Here's how to add a monitor for `https://laravel.com`:

```php
php artisan monitor:create https://laravel.com
```

You will be asked if the uptime check should look for a specific string on the response. This is handy if you know a few words that appear on the url you want to monitor. If you choose to specify a string and the string is not contained in the response when checking the url, the package will consider that uptime check failed.

If the url you want to monitor starts with `https://` the package will also [start monitoring](https://docs.spatie.be/laravel-uptime-monitor/v3/monitoring-ssl-certificates/getting-started) the ssl certificate of your site.

You've just set up your first monitor. Congratulations! The package will now send you [notifications](https://docs.spatie.be/laravel-uptime-monitor/v3/monitoring-uptime/notifications) when your monitor fails and when it is restored.
 
Read the [high level overview section](https://docs.spatie.be/laravel-uptime-monitor/v3/high-level-overview) to learn how the uptime checking works in detail.

Instead of using the `monitor:create` command you may also manually create a row in the `monitors` table. Here's [a description of all the fields in that table](https://docs.spatie.be/laravel-uptime-monitor/v3/advanced-usage/manually-modifying-monitors).
 
## Removing a monitor
 
You can remove a monitor by running `monitor:delete`. Here's how to delete the monitor for `https://laravel.com`:
 
```php
php artisan monitor:delete https://laravel.com
```
 
This will remove the monitor for laravel.com from the database. Want to delete multiple monitors at once? Just pass all the urls as comma-separated list.

Instead of using the `monitor:delete` command you may also manually delete the relevant row in the `monitors` table.
