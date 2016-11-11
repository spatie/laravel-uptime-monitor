<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\DownSites;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\HealthySites;
use Spatie\UptimeMonitor\Commands\UptimeMonitorLists\SitesWithSslProblems;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class ListUptimeMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all uptime monitors';

    public function handle()
    {
        (new DownSites($this))->display();
        (new SitesWithSslProblems($this))->display();
        (new HealthySites($this))->display();
    }
}
