<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;

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
        $rows = Site::all()->map(function (Site $site) {
            $url = $site->url;

            $reachable = $site->status === UptimeStatus::UP ? Emoji::ok() : Emoji::notOk();

            return compact('url', 'reachable');
        });

        $titles = ['URL', 'Reachable'];

        $this->table($titles, $rows);
    }
}
