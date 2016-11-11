<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\DownSites;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\HealthySites;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\SitesWithSslProblems;

class ListUptimeMonitors extends Command
{
    protected $signature = 'uptime-monitor:list';

    protected $description = 'List all uptime monitors';

    public function handle()
    {
        (new DownSites($this))->display();
        (new SitesWithSslProblems($this))->display();
        (new HealthySites($this))->display();
    }
}
