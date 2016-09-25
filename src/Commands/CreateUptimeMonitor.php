<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\Site;

class CreateUptimeMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an uptime monitor';

    public function handle()
    {
        $this->warn("Let's create your new uptime monitor!");

        $url = $this->ask("Which url to you want to monitor? Should start with either 'http://' or 'https://'");

        if ($this->confirm('Should we look for a specific string on the response?')) {
            $lookForString = $this->ask("Which string?");
        }

        $site = Site::create([
            'url' => $url,
            'look_for_string' => $lookForString ?? '',
        ]);

        $this->warn("A new uptime monitor for {$site->url} was created!");

    }
}
