<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class Unchecked
{
    public static function display()
    {
        $uncheckedMonitors = MonitorRepository::getUnchecked();

        if (! $uncheckedMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Not yet checked');
        ConsoleOutput::warn('===============');

        $rows = $uncheckedMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            return compact('url');
        });

        $titles = ['URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
