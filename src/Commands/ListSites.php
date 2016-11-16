<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Commands\MonitorLists\FailedMonitors;
use Spatie\UptimeMonitor\Commands\MonitorLists\HealthyMonitors;
use Spatie\UptimeMonitor\Commands\MonitorLists\MonitorsReportingSslProblems;
use Spatie\UptimeMonitor\Commands\MonitorLists\UncheckedMonitors;
use Spatie\UptimeMonitor\MonitorRepository;

class ListSites extends BaseCommand
{
    protected $signature = 'sites:list';

    protected $description = 'List all sites';

    public function handle()
    {
        $this->line('');

        if (! MonitorRepository::getAllEnabledMonitors()->count()) {
            $this->warn('There are no sites configured or enabled.');
            $this->info('You can add a site using the `sites:add` command');
        }

        UncheckedMonitors::display();
        FailedMonitors::display();
        MonitorsReportingSslProblems::display();
        HealthyMonitors::display();
    }
}
