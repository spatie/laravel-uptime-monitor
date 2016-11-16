<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class HealthyMonitors
{
    public static function display()
    {
        $healthySites = MonitorRepository::healthyMonitors();

        if (! $healthySites->count()) {
            return;
        }

        ConsoleOutput::info('Healthy monitors');
        ConsoleOutput::info('================');

        $rows = $healthySites->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reachable = $monitor->reachableAsEmoji;

            $onlineSince = $monitor->formattedLastUpdatedStatusChangeDate;

            if ($monitor->check_ssl_certificate) {
                $sslCertificateFound = Emoji::ok();
                $sslCertificateExpirationDate = $monitor->formattedSslCertificateExpirationDate;
                $sslCertificateIssuer = $monitor->ssl_certificate_issuer;
            }


            return compact('url', 'reachable', 'onlineSince', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Online since', 'SSL Certifcate', 'SSL Expiration date', 'SSL Issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
