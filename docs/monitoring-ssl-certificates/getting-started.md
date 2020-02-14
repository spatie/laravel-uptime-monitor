---
title: Getting started
weight: 1
---

This package can monitor the validity of ssl certificates. It can notify you when an invalid certificate is found. It can also warn you if a certificate is going to expire soon.

To get started you should [create a monitor](https://docs.spatie.be/laravel-uptime-monitor/v3/monitoring-uptime/getting-started#creating-your-first-monitor). To make life easy for you, if the url starts with `https://` the package will automatically enable a certificate check.

If you want to run an certificate check without running the uptime check you can set `uptime_check_enabled` to `0` in the relevant row in the `monitors` table.
