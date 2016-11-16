<?php

namespace Spatie\UptimeMonitor\Commands\SiteLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class SitesWithSslProblems
{
    public static function display()
    {
        $sitesWithSslProblems = SiteRepository::withSslProblems();

        if (! $sitesWithSslProblems->count()) {
            return;
        }

        ConsoleOutput::warn('Sites with ssl certificate problems');
        ConsoleOutput::warn('===================================');

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
