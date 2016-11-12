<?php

namespace Spatie\UptimeMonitor\Commands\SiteLists;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class UncheckedSites
{
    protected $output;

    public function __construct(Command $output)
    {
        $this->output = $output;
    }

    public function display()
    {
        $downSites = SiteRepository::uncheckedSites();

        if (! $downSites->count()) {
            return;
        }

        $this->output->info('Sites that have not been checked yet');
        $this->output->info('====================================');

        $rows = $downSites->map(function (Site $site) {
            $url = $site->url;

            return compact('url');
        });

        $titles = ['URL'];

        $this->output->table($titles, $rows);
    }
}
