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
            
            if ($scheme = $this->choice('What protocol do we need?', [1 => 'http', 2 => 'https'], 2)) {
                $url = $url->withScheme($scheme);
            }
        }

        if ($this->confirm('Should we look for a specific string on the response?')) {
            $lookForString = $this->ask('Which string?');
        }

        $monitor = Monitor::create([
            'url' => trim($url, '/'),
            'look_for_string' => $lookForString ?? null,
            'uptime_check_method' => isset($lookForString) ? 'get' : 'head',
            'certificate_check_enabled' => $url->getScheme() === 'https',
        ]);

        $this->warn("{$monitor->url} will be monitored!");
    }
}
