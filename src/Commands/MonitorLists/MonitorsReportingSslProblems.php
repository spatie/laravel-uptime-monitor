<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class MonitorsReportingSslProblems
{
    public static function display()
    {
        $sitesWithSslProblems = SiteRepository::withSslProblems();

        if (! $sitesWithSslProblems->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors reporting SSL Certificate problems');
        ConsoleOutput::warn('===========================================');

        $rows = $sitesWithSslProblems->map(function (Site $site) {
            $url = $site->url;

            $reason = $site->chunkedLastSslFailureReason;

            return compact('url', 'reason');
        });

        $titles = ['URL', 'Problem description'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
