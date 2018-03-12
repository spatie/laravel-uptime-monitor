<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class CheckUptime extends BaseCommand
{
    protected $signature = 'monitor:check-uptime
                            {--url= : Only check these urls}
                            {--f|force : Force run all monitors }';

    protected $description = 'Check the uptime of all sites';

    public function handle()
    {
        $monitors = $this->option('force') ? MonitorRepository::getEnabled() : MonitorRepository::getForUptimeCheck();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the uptime of '.count($monitors).' monitors...');

        $monitors->checkUptime();

        $this->info('All done!');
    }
}
