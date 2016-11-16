<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class VirginMonitors
{
    public static function display()
    {
        $downSites = SiteRepository::uncheckedSites();

        if (! $downSites->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors that have not been used yet');
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
