<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\SiteRepository;

class CheckUptime extends BaseCommand
{
    protected $signature = 'sites:check-uptime';

    protected $description = 'Check the uptime of all sites';

    public function handle()
    {
        $sites = SiteRepository::getAllForUptimeCheck();

        $this->comment('Start checking the uptime of '.count($sites).' sites...');

        $sites->checkUptime();

        $this->info('All done!');
    }
}
