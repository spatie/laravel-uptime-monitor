<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;

class DeleteMonitor extends BaseCommand
{
    protected $signature = 'monitor:delete {url}';

    protected $description = 'Delete a monitor';

    public function handle()
    {
        $url = $this->argument('url');

        $monitor = Monitor::where('url', $url)->first();

        if (! $monitor) {
            $this->error("Monitor {$url} is not configured");

            return;
        }

        if ($this->confirm("Are you sure you want stop monitoring {$monitor->url}?")) {
            $monitor->delete();

            $this->warn("{$monitor->url} will not be monitored anymore");
        }
    }
}
