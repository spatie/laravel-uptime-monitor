<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\UptimeMonitor;

class ListUptimeMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all uptime monitors';

    public function handle()
    {
        $rows = UptimeMonitor::all()->map(function (UptimeMonitor $uptimeMonitor) {
            $url = $uptimeMonitor->url;

            return compact('url');
        });

        $titles = ['url'];

        $this->table($titles, $rows);
    }
}
