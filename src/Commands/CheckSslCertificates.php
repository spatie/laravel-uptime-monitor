<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class CheckSslCertificates extends BaseCommand
{
    protected $signature = 'sites:check-ssl
                           {--url= : Only check these urls}';


    protected $description = 'Check the ssl certificates of all sites';

    public function handle()
    {
        $monitors = MonitorRepository::getAllForSslCheck();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the ssl certificate of '.count($monitors).' sites...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking ssl-certificate of {$monitor->url}");

            $monitor->checkSslCertificate();

            if ($monitor->ssl_certificate_status !== SslCertificateStatus::VALID) {
                $this->error("Could not download certificate of {$monitor->url} because: {$monitor->ssl_certificate_failure_reason}");
            }
        });

        $this->info('All done!');
    }
}
