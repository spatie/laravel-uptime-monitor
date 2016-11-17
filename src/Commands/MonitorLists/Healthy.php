<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class Healthy
{
    public static function display()
    {
        $healthyMonitor = MonitorRepository::getHealthy();

        if (! $healthyMonitor->count()) {
            return;
        }

        ConsoleOutput::info('Healthy monitors');
        ConsoleOutput::info('================');

        $rows = $healthyMonitor->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reachable = $monitor->reachableAsEmoji;

            $onlineSince = $monitor->formattedLastUpdatedStatusChangeDate;

            if ($monitor->ssl_certificate_check_enabled) {
                $sslCertificateFound = $monitor->sslCertificateStatusAsEmoji;
                $sslCertificateExpirationDate = $monitor->formattedSslCertificateExpirationDate;
                $sslCertificateIssuer = $monitor->ssl_certificate_issuer;
            }

            return compact('url', 'reachable', 'onlineSince', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Uptime check', 'Online since', 'SSL Certificate check', 'SSL Expiration date', 'SSL Issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
