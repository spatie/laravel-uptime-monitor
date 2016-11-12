<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\Url\Url;

class CreateSite extends Command
{
    protected $signature = 'sites:create';

    protected $description = 'Create a site to monitor';

    public function handle()
    {
        $this->warn("Let's create your new uptime monitor!");

        $url = $this->ask("Which url to you want to monitor? Should start with either 'http://' or 'https://'");

        $url = Url::fromString($url);

        if (! in_array($url->getScheme(), ['http', 'https'])) {
            $this->error('The given url did not start with `http://` or `https://`.');

            return;
        }

        if ($this->confirm('Should we look for a specific string on the response?')) {
            $lookForString = $this->ask('Which string?');
        }

        $site = Site::create([
            'url' => $url,
            'look_for_string' => $lookForString ?? '',
            'uptime_check_method' => $lookForString === '' ? 'head' : 'get',
            'check_ssl_certificate' => $url->getScheme() === 'https',
        ]);

        $this->warn("A new uptime monitor for {$site->url} was created!");
    }
}
