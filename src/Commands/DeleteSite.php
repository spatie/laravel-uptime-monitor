<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;

class DeleteSite extends BaseCommand
{
    protected $signature = 'sites:delete {url}';

    protected $description = 'Stop monitoring a site by deleting it from the database';

    public function handle()
    {
        $url = $this->argument('url');

        $monitor = Monitor::where('url', $url)->first();

        if (! $monitor) {
            $this->error("Site {$url} is not configured");

            return;
        }

        if ($this->confirm("Are you sure you want stop monitoring {$monitor->url}?")) {
            $monitor->delete();

            $this->warn("{$monitor->url} will not be monitored anymore");
        }
    }
}
