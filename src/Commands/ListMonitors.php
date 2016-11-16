<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Commands\MonitorLists\DisabledMonitors;
use Spatie\UptimeMonitor\Commands\MonitorLists\FailedMonitors;
use Spatie\UptimeMonitor\Commands\MonitorLists\HealthyMonitors;
use Spatie\UptimeMonitor\Commands\MonitorLists\MonitorsReportingSslProblems;
use Spatie\UptimeMonitor\Commands\MonitorLists\UncheckedMonitors;
use Spatie\UptimeMonitor\MonitorRepository;

class ListMonitors extends BaseCommand
{
    protected $signature = 'monitor:list';

    protected $description = 'List all monitors';

    public function handle()
    {
        $this->line('');

        if (! MonitorRepository::getEnabled()->count()) {
            $this->warn('There are no monitors created or enabled.');
            $this->info('You create a monitor using the `monitor:create` command');
        }

        UncheckedMonitors::display();
        DisabledMonitors::display();
        FailedMonitors::display();
        MonitorsReportingSslProblems::display();
        HealthyMonitors::display();
    }
}
