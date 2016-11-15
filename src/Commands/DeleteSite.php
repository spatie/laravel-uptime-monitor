<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\Site;

class DeleteSite extends BaseCommand
{
    protected $signature = 'sites:delete {url}';

    protected $description = 'Stop monitoring a site by deleting it from the database';

    public function handle()
    {
        $url = $this->argument('url');

        $site = Site::where('url', $url)->first();

        if (! $site) {
            $this->error("Site {$url} is not configured");

            return;
        }

        if ($this->confirm("Are you sure you want stop monitoring {$site->url}?")) {
            $site->delete();

            $this->warn("{$site->url} will not be monitored anymore");
        }
    }
}
