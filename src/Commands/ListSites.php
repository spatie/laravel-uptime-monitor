<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\DownSites;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\HealthySites;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\SitesWithSslProblems;

class ListSites extends Command
{
    protected $signature = 'sites:list';

    protected $description = 'List all sites';

    public function handle()
    {
        (new DownSites($this))->display();
        (new SitesWithSslProblems($this))->display();
        (new HealthySites($this))->display();
    }
}
