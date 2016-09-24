<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Models\UptimeMonitor;
use Spatie\UptimeMonitor\Services\PingMonitors\UptimeMonitorCollection;

class CheckUptimeMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all uptime monitors';

    public function handle()
    {
        $uptimeMonitors = UptimeMonitor::all()->filter(function (UptimeMonitor $uptimeMonitor) {
            return $uptimeMonitor->shouldCheck();
        });

        $this->comment('Need to check '.count($uptimeMonitors).' sites...');

        $uptimeMonitorCollection = new UptimeMonitorCollection($uptimeMonitors);

        $uptimeMonitorCollection->check();

        $this->info('All done!');
    }
}
