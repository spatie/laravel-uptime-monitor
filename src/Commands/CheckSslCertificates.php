<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class CheckSslCertificates extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:check-ssl';

    /**
     * The console command description.
     *
     * @var string
     */
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
