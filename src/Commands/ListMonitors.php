<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Commands\MonitorLists\CertificateCheckFailed;
use Spatie\UptimeMonitor\Commands\MonitorLists\Disabled;
use Spatie\UptimeMonitor\Commands\MonitorLists\Healthy;
use Spatie\UptimeMonitor\Commands\MonitorLists\Unchecked;
use Spatie\UptimeMonitor\Commands\MonitorLists\UptimeCheckFailed;
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
            $this->info('You create a monitor using the `monitor:create {url}` command');
        }

        Unchecked::display();
        Disabled::display();
        UptimeCheckFailed::display();
        CertificateCheckFailed::display();
        Healthy::display();
    }
}
