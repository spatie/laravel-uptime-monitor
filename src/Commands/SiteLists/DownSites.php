<?php

namespace Spatie\UptimeMonitor\Commands\SiteLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class DownSites
{
    public static function display()
    {
        $downSites = SiteRepository::downSites();

        if (! $downSites->count()) {
            return;
        }

        ConsoleOutput::warn('Sites that are down');
        ConsoleOutput::warn('===================');

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

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
