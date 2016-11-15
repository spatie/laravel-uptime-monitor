<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;
use Spatie\SslCertificate\SslCertificate;
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

        if($url = $this->option('url')) {
            $sites = $sites->filter(function(Site $site) use ($url) {
                return in_array((string)$site->url, explode(',', $url));
            });
        };

        $this->comment('Start checking the ssl certificate of '.count($sites).' sites...');

        $sites->each(function (Site $site) {
            $this->info("Checking ssl-certificate of {$site->url}");

            try {
                $certificate = SslCertificate::createForHostName($site->url->getHost());

                $site->updateWithCertificate($certificate);
            } catch (CouldNotDownloadCertificate $exception) {
                $this->error("Could not download certificate of {$site->url} because: {$exception->getMessage()}");
                $site->updateWithCertificateException($exception);
            }
        });

        $this->info('All done!');
    }
}
