<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class CheckSslCertificates extends BaseCommand
{
    protected $signature = 'uptime-monitor:check-ssl';

    protected $description = 'Check the ssl certificates of all sites';

    public function handle()
    {
        $sites = SiteRepository::getAllForSslCheck();

        $this->comment('Start checking the ssl certificate of '.count($sites).' sites...');

        SiteRepository::getAllForSslCheck()->each(function (Site $site) {
            $this->info("Checking ssl-certificate of {$site->url}");

            try {
                $certificate = SslCertificate::createForHostName($site->url->getHost());

                $site->updateWithCertificate($certificate);
            }
            catch(CouldNotDownloadCertificate $exception) {
                $this->error("Could not download certifcate of {$site->url} because: {$exception->getMessage()}");
                $site->updateWithCertificateException($exception);
            }
        });

        $this->info('All done!');
    }
}
