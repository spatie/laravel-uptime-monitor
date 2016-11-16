<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class HealthyMonitors
{
    public static function display()
    {
        $healthySites = SiteRepository::healthySites();

        if (! $healthySites->count()) {
            return;
        }

        ConsoleOutput::info('Healthy sites');
        ConsoleOutput::info('=============');

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

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
