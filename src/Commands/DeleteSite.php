<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\Site;

class DeleteSite extends Command
{
    protected $signature = 'sites:delete';

    protected $description = 'Stop monitoring a site';

    public function handle()
    {
        $this->warn("Let's create your new uptime monitor!");

        $url = $this->ask('Specify the url of the uptime monitor that should be deleted');

        $site = Site::where('url', $url)->first();

        if (! $site) {
            $this->error("There is no uptime monitor for url {$url}");

            return;
        }

        if ($this->confirm("Are you sure you want to delete the uptime monitor for {$site->url}?")) {
            $site->delete();

            $this->warn("Uptime monitor {$url} deleted!");
        }
    }
}
