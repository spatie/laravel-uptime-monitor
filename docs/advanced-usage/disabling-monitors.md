---
title: Disabling monitors
weight: 5
---

If you want to temporarily stop the uptime and the certificate check of a monitor you can disable it.

This is how to disable a monitor for `https://laravel.com`

```bash
php artisan monitor:disable https://laravel.com
```

You can re-enable the checks of a monitor with:

```bash
php artisan monitor:enable https://laravel.com
```

Both commands accept multiple urls comma-separated to enable or disable multiple monitors at once.
