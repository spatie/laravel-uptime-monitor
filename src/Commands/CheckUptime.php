<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\SiteRepository;

class CheckUptime extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:check-uptime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the uptime of all sites';

    public function handle()
    {
        $sites = SiteRepository::getAllForUptimeCheck();

        $this->comment('Start checking the uptime of '.count($sites).' sites...');

        $sites->checkUptime();

        $this->info('All done!');
    }
}
