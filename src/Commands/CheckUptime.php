<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class CheckUptime extends BaseCommand
{
    protected $signature = 'sites:check-uptime  
                            {--url= : Only check these urls}';

    protected $description = 'Check the uptime of all sites';

    public function handle()
    {
        $sites = SiteRepository::getAllForUptimeCheck();

        if ($url = $this->option('url')) {
            $sites = $sites->filter(function (Site $site) use ($url) {
                return in_array((string) $site->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the uptime of '.count($sites).' sites...');

        $sites->checkUptime();

        $this->info('All done!');
    }
}
