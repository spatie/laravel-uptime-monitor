<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Site;
use Spatie\Url\Url;

class AddSite extends BaseCommand
{
    protected $signature = 'sites:add {url}';

    protected $description = 'Add a site to monitor';

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

        $site = Site::create([
            'url' => trim($url, '/'),
            'look_for_string' => $lookForString ?? '',
            'uptime_check_method' => isset($lookForString) ? 'get' : 'head',
            'check_ssl_certificate' => $url->getScheme() === 'https',
            'uptime_check_interval_in_minutes' => config('laravel-uptime-monitor.uptime_check.run_interval_in_minutes'),
        ]);

        $this->warn("{$site->url} will be monitored!");
    }
}
