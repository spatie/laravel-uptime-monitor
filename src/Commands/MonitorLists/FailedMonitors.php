<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class FailedMonitors
{
    public static function display()
    {
        $downSites = MonitorRepository::failingMonitors();

        if (! $downSites->count()) {
            return;
        }

        ConsoleOutput::warn('Monitors that have failed');
        ConsoleOutput::warn('=========================');

        $rows = $downSites->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reachable = $monitor->reachableAsEmoji;

            $offlineSince = $monitor->formattedLastUpdatedStatusChangeDate;

            $reason = $monitor->chunkedLastFailureReason;

            if ($monitor->check_ssl_certificate) {
                $sslCertificateFound = Emoji::ok();
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
