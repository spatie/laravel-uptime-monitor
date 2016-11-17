<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class FailedMonitors
{
    public static function display()
    {
        $failingMonitors = MonitorRepository::getFailing();

        if (! $failingMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors that have failed');
        ConsoleOutput::warn('=========================');

        $rows = $failingMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reachable = $monitor->reachableAsEmoji;

            $offlineSince = $monitor->formattedLastUpdatedStatusChangeDate;

            $reason = $monitor->chunkedLastFailureReason;

            if ($monitor->ssl_certificate_check_enabled) {
                $sslCertificateFound = $monitor->sslCertificateStatusAsEmoji;
                $sslCertificateExpirationDate = $monitor->formattedSslCertificateExpirationDate;
                $sslCertificateIssuer = $monitor->ssl_certificate_issuer;
            }

            return compact('url', 'reachable', 'offlineSince', 'reason', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Offline since', 'Reason', 'SSL Certificate', 'SSL Expiration date', 'SSL Issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
