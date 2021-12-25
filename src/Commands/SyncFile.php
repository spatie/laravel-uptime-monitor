<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Support\Str;
use Spatie\UptimeMonitor\Exceptions\CannotSaveMonitor;
use Spatie\UptimeMonitor\Models\Monitor;

class SyncFile extends BaseCommand
{
    protected $signature = 'monitor:sync-file
                            {path : Path to JSON file with monitors}
                            {--delete-missing : Delete monitors from the database if they\'re not found in the monitors file}';

    protected $description = 'One way sync monitors from JSON file to database';

    public function handle()
    {
        $json = file_get_contents($this->argument('path'));

        $monitorsInFile = collect(json_decode($json, true));

        $this->validateMonitors($monitorsInFile);

        $this->createOrUpdateMonitorsFromFile($monitorsInFile);

        $this->deleteMissingMonitors($monitorsInFile);
    }

    protected function validateMonitors($monitorsInFile)
    {
        $monitorsInFile->each(function ($monitorAttributes) {
            if (! Str::startsWith($monitorAttributes['url'], ['https://', 'http://'])) {
                throw new CannotSaveMonitor("URL `{$monitorAttributes['url']}` is invalid (is the URL scheme included?)");
            }
        });
    }

    protected function createOrUpdateMonitorsFromFile($monitorsInFile)
    {
        $monitorsInFile
            ->each(function ($monitorAttributes) {
                $this->createOrUpdateMonitor($monitorAttributes);
            });

        $this->info("Synced {$monitorsInFile->count()} monitor(s) to database");
    }

    protected function createOrUpdateMonitor(array $monitorAttributes)
    {
        Monitor::firstOrNew([
            'url' => $monitorAttributes['url'],
        ])
            ->fill($monitorAttributes)
            ->save();
    }

    protected function deleteMissingMonitors($monitorsInFile)
    {
        if (! $this->option('delete-missing')) {
            return;
        }

        Monitor::all()
            ->reject(function (Monitor $monitor) use ($monitorsInFile) {
                return $monitorsInFile->contains('url', $monitor->url);
            })
            ->each(function (Monitor $monitor) {
                $path = $this->argument('path');
                $this->comment("Deleted monitor for `{$monitor->url}` from database because it was not found in `{$path}`");
                $monitor->delete();
            });
    }
}
