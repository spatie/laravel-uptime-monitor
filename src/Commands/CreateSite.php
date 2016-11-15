<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\Url\Url;

class CreateSite extends Command
{
    protected $signature = 'sites:create {url}';

    protected $description = 'Add a site to monitor';

    public function handle()
    {
        $this->warn("Let's create your new uptime monitor!");

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
            'uptime_check_method' => isset($lookForString)  ? 'get' : 'head',
            'check_ssl_certificate' => $url->getScheme() === 'https',
        ]);

        $this->warn("{$site->url} will be monitored!");
    }
}
