<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class EnableMonitor extends BaseCommand
{
    protected $signature = 'monitor:enable {url}';

    protected $description = 'Enable a monitor';

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
            $this->enableMonitor(trim($url));
        }
    }

    protected function enableMonitor(string $url)
    {
        if (!$monitor = MonitorRepository::findByUrl($url)) {
            $this->error("There is no monitor configured for url `{$url}`.");

            return;
        }

        $monitor->enable();

        $this->info("The checks for url `{$url}` are now enabled.");
    }
}
