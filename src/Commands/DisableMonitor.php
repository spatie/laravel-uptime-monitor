<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class DisableMonitor extends BaseCommand
{
    protected $signature = 'monitor:disable {url}';

    protected $description = 'Disable a monitor';

    public function handle()
    {
        $urls = null;

        if ($this->argument('url') == 'all') {
            $urls = Monitor::get()->pluck('url');
        }

        if (is_null($urls)) {
            $urls = explode(',', $this->argument('url'));
        }

        foreach ($urls as $url) {
            $this->disableMonitor(trim($url));
        }
    }

    protected function disableMonitor(string $url)
    {
        if (!$monitor = MonitorRepository::findByUrl($url)) {
            $this->error("There is no monitor configured for url `{$url}`.");

            return;
        }

        $monitor->disable();

        $this->info("The checks for url `{$url}` are now disabled.");
    }
}
