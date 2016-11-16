<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class UncheckedMonitors
{
    public static function display()
    {
        $downSites = MonitorRepository::getAllUnchecked();

        if (! $downSites->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors that have not been used yet');
        ConsoleOutput::warn('====================================');

        $rows = $downSites->map(function (Monitor $monitor) {
            $url = $monitor->url;

            return compact('url');
        });

        $titles = ['URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
