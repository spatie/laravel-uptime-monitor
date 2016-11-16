<?php

namespace Spatie\UptimeMonitor\Commands\SiteLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class UncheckedSites
{
    public static function display()
    {
        $downSites = SiteRepository::uncheckedSites();

        if (! $downSites->count()) {
            return;
        }

        ConsoleOutput::warn('Sites that have not been checked yet');
        ConsoleOutput::warn('====================================');

        $rows = $downSites->map(function (Site $site) {
            $url = $site->url;

            return compact('url');
        });

        $titles = ['URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
