<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\MonitorRepository;

class EnableMonitor extends BaseCommand
{
    protected $signature = 'monitor:enable {url}';

    protected $description = 'Enable a monitors';

    public function handle()
    {
        foreach (explode(',', $this->argument('url')) as $url) {
            $this->enableMonitor(trim($url));
        }
    }

    protected function enableMonitor(string $url)
    {
        if (! $monitor = MonitorRepository::findByUrl($url)) {
            $this->error("There is no monitor configured for url `{$url}`.");

            return;
        }

        if ($monitor->enabled) {
            $this->warn("The monitor for url `{$url}` was already enabled.");

            return;
        }

        $monitor->enable();
        $this->info("The monitor for url `{$url}` is now enabled");
    }
}
