<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class UptimeCheckFailed
{
    public static function display()
    {
        $failingMonitors = MonitorRepository::getWithFailingUptimeCheck();

        if (! $failingMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Uptime check failed');
        ConsoleOutput::warn('===================');

        $rows = $failingMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reachable = $monitor->reachableAsEmoji;

            $offlineSince = $monitor->formattedLastUpdatedStatusChangeDate;

            $reason = $monitor->chunkedLastFailureReason;

            if ($monitor->certificate_check_enabled) {
                $certificateFound = $monitor->certificateStatusAsEmoji;
                $certificateExpirationDate = $monitor->formattedCertificateExpirationDate;
                $certificateIssuer = $monitor->certificate_issuer;
            }

            return compact('url', 'reachable', 'offlineSince', 'reason', 'certificateFound', 'certificateExpirationDate', 'certificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Offline since', 'Reason', 'Certificate', 'Certificate expiration date', 'Certificate issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
