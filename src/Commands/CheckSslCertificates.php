<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class CheckSslCertificates extends BaseCommand
{
    protected $signature = 'sites:check-ssl
                           {--url= : Only check these urls}';


    protected $description = 'Check the ssl certificates of all sites';

    public function handle()
    {
        $sites = SiteRepository::getAllForSslCheck();

        if ($url = $this->option('url')) {
            $sites = $sites->filter(function (Site $site) use ($url) {
                return in_array((string) $site->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the ssl certificate of '.count($sites).' sites...');

        $sites->each(function (Site $site) {
            $this->info("Checking ssl-certificate of {$site->url}");

            $site->checkSslCertificate();

            if ($site->ssl_certificate_status !== SslCertificateStatus::VALID) {
                $this->error("Could not download certificate of {$site->url} because: {$site->ssl_certificate_failure_reason}");
            }
        });

        $this->info('All done!');
    }
}
