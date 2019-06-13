---
title: Using your own model
weight: 6
---

By default this package uses the `Spatie\UptimeMonitor\Models\Monitor` model. If you want add some extra functionality you can specify your own model in the `monitor_model` key of the config file. The only requirement for your custom model is that is should extend `Spatie\UptimeMonitor\Models\Monitor`.
