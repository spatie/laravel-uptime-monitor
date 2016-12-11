<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\Url\Url;
use Spatie\UptimeMonitor\Models\Monitor;

class CreateMonitor extends BaseCommand
{
    protected $signature = 'monitor:create {url}';

    protected $description = 'Create a monitor';

    public function handle()
    {
        $url = Url::fromString($this->argument('url'));

        if (! in_array($url->getScheme(), ['http', 'https'])) {
            $this->error('The given url did not start with `http://` or `https://`.');

            return;
        }

        if ($this->confirm('Should we look for a specific string on the response?')) {
            $lookForString = $this->ask('Which string?');
        }

        $monitor = Monitor::create([
            'url' => trim($url, '/'),
            'look_for_string' => $lookForString ?? '',
            'uptime_check_method' => isset($lookForString) ? 'get' : 'head',
            'certificate_check_enabled' => $url->getScheme() === 'https',
            'uptime_check_interval_in_minutes' => config('laravel-uptime-monitor.uptime_check.run_interval_in_minutes'),
        ]);

        $this->warn("{$monitor->url} will be monitored!");
    }
}
