<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

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
        $this->listSitesThatAreDown();
        $this->listSitesWithSslProblems();
        $this->listHealthySites();
    }

    public function listSitesThatAreDown()
    {
        $downSites = SiteRepository::downSites();

        if (! $downSites->count()) {
            return;
        }

        $this->info('Sites that are down');
        $this->info('===================');

        $rows = $downSites->map(function (Site $site) {
            $url = $site->url;

            $reachable = $site->reachableAsEmoji;

            $offlineSince = $site->formattedLastUpdatedStatusChangeDate;

            $reason = $site->chunkedLastFailureReason;

            if ($site->check_ssl_certificate) {
                $sslCertificateFound = Emoji::ok();
                $sslCertificateExpirationDate = $site->formattedSslCertificateExpirationDate;
                $sslCertificateIssuer = $site->ssl_certificate_issuer;
            }

            return compact('url', 'reachable', 'offlineSince', 'reason', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Offline since', 'Reason', 'SSL Certificate', 'SSL Expiration date', 'SSL Issuer'];

        $this->table($titles, $rows);
    }

    protected function listSitesWithSslProblems()
    {
        $sitesWithSslProblems = SiteRepository::withSslProblems();

        if (! $sitesWithSslProblems->count()) {
            return;
        }

        $rows = $sitesWithSslProblems->map(function (Site $site) {
            $url = $site->url;

            $reachable = $site->reachableAsEmoji;

            $sslCertificateFound = Emoji::notOk();
            $sslCertificateExpirationDate = $site->formattedSslCertificateExpirationDate;
            $sslCertificateIssuer = $site->ssl_certificate_issuer ?? 'Unknown';


            return compact('url', 'reachable', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });
    }

    public function listHealthySites()
    {
        $healthySites = SiteRepository::healthySites();

        if (! $healthySites->count()) {
            return;
        }

        $this->info('Healthy sites');
        $this->info('============');

        $rows = $healthySites->map(function (Site $site) {
            $url = $site->url;

            $reachable = $site->reachableAsEmoji;

            $onlineSince = $site->formattedLastUpdatedStatusChangeDate;

            if ($site->check_ssl_certificate) {
                $sslCertificateFound = Emoji::ok();
                $sslCertificateExpirationDate = $site->formattedSslCertificateExpirationDate;
                $sslCertificateIssuer = $site->ssl_certificate_issuer;
            }


            return compact('url', 'reachable', 'onlineSince', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Online since', 'SSL Certifcate', 'SSL Expiration date', 'SSL Issuer'];

        $this->table($titles, $rows);
    }
}
