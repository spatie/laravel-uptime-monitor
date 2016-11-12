<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Commands\SiteLists\DownSites;
use Spatie\UptimeMonitor\Commands\SiteLists\HealthySites;
use Spatie\UptimeMonitor\Commands\SiteLists\SitesWithSslProblems;
use Spatie\UptimeMonitor\Commands\SiteLists\UncheckedSites;

class ListSites extends Command
{
    protected $signature = 'sites:list';

    protected $description = 'List all sites';

    public function handle()
    {
        (new UncheckedSites($this))->display();
        (new DownSites($this))->display();
        (new SitesWithSslProblems($this))->display();
        (new HealthySites($this))->display();
    }
}
