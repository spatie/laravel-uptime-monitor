<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class DisabledMonitors
{
    public static function display()
    {
        $disabledMonitors = MonitorRepository::getDisabled();

        if (! $disabledMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors that have been disabled');
        ConsoleOutput::warn('================================');

        $rows = $disabledMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            return compact('url');
        });

        $titles = ['URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
