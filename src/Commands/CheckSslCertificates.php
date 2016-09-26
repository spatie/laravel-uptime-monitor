<?php

namespace Spatie\UptimeMonitor\Commands;

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

            $certificate = SslCertificate::createForHostName($site->url->getHost());

            $site->ssl_certificate_status = $certificate->isValid()
                ? SslCertificateStatus::VALID
                : SslCertificateStatus::INVALID;

            $site->ssl_certificate_expiration_date = $certificate->expirationDate();

            $site->ssl_certificate_issuer = $certificate->getIssuer();

            $site->save();
        });

        $this->info('All done!');
    }
}
