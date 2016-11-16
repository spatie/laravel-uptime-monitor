<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\Url\Url;

class EnableMonitor extends BaseCommand
{
    protected $signature = 'monitor:enable {url}';

    protected $description = 'Enable a monitor';

    public function handle()
    {
        $url = Url::fromString($this->argument('url'));


    }
}
