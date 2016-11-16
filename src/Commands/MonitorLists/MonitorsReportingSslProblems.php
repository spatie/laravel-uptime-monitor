<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class MonitorsReportingSslProblems
{
    public static function display()
    {
        $monitorsWithSslProblems = MonitorRepository::getAllWithSslProblems();

        if (! $monitorsWithSslProblems->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors reporting SSL Certificate problems');
        ConsoleOutput::warn('===========================================');

        $rows = $monitorsWithSslProblems->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reason = $monitor->chunkedLastSslFailureReason;

            return compact('url', 'reason');
        });

        $titles = ['URL', 'Problem description'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
