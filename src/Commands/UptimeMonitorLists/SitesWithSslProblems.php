<?php

namespace Spatie\UptimeMonitor\Commands\UptimeMonitorLists;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class SitesWithSslProblems
{
    protected $output;

    public function __construct(Command $output)
    {
        $this->output = $output;
    }

    public function display()
    {
        $sitesWithSslProblems = SiteRepository::withSslProblems();

        if (! $sitesWithSslProblems->count()) {
            return;
        }

        $this->output->info('Sites with ssl problems');
        $this->output->info('=======================');

        $rows = $sitesWithSslProblems->map(function (Site $site) {
            $url = $site->url;

            $reachable = $site->reachableAsEmoji;

            $sslCertificateFound = Emoji::notOk();
            $sslCertificateExpirationDate = $site->formattedSslCertificateExpirationDate;
            $sslCertificateIssuer = $site->ssl_certificate_issuer ?? 'Unknown';


            return compact('url', 'reachable', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'SSL Certificate', 'SSL Expiration date', 'SSL Issuer'];

        $this->output->table($titles, $rows);
    }
}
