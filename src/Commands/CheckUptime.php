<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class CheckUptime extends BaseCommand
{
    protected $signature = 'monitor:check-uptime  
                            {--url= : Only check these urls}';

    protected $description = 'Check the uptime of all sites';

    public function handle()
    {
        $monitors = MonitorRepository::getAllForUptimeCheck();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the uptime of '.count($monitors).' sites...');

        $monitors->checkUptime();

        $this->info('All done!');
    }
}
