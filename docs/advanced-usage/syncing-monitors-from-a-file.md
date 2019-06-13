---
title: Syncing monitors from a file
weight: 2
---

Using the `monitor:create` becomes tedious fast if you have a large number of urls that you wish to monitor. Luckily there's also a command to bulk import urls from a json file:

```
php artisan monitor:sync-file <path-to-file>
```

Here's an example of the structure that json file should have:

```json
[
  {
    "url": "https://www.example.com",
    "uptime_check_enabled": true,
    "certificate_check_enabled": true
  },
  {
    "url": "http://www.another-example.com",
    "uptime_check_enabled": true,
    "certificate_check_enabled": false
  }
]
```

By default the command will import all missing urls and update existing urls. If you wish to delete urls from the database that are not in the json file you can use the `--delete-missing` flag.
