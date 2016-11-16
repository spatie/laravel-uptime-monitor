<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Commands\SiteLists\DownSites;
use Spatie\UptimeMonitor\Commands\SiteLists\HealthySites;
use Spatie\UptimeMonitor\Commands\SiteLists\SitesWithSslProblems;
use Spatie\UptimeMonitor\Commands\SiteLists\UncheckedSites;
use Spatie\UptimeMonitor\SiteRepository;

class ListSites extends BaseCommand
{
    protected $signature = 'sites:list';

    protected $description = 'List all sites';

    public function handle()
    {
        $this->line('');

        if (! SiteRepository::getAllEnabledSites()->count()) {
            $this->warn('There are no sites configured or enabled.');
            $this->info('You can add a site using the `sites:add` command');
        }

        UncheckedSites::display();
        DownSites::display();
        SitesWithSslProblems::display();
        HealthySites::display();
    }
}
