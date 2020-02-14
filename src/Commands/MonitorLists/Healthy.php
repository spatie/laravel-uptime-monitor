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
            $certificateFound = '';
            $certificateExpirationDate = '';
            $certificateIssuer = '';

            $url = $monitor->url;

            $reachable = $monitor->uptimeStatusAsEmoji;

            $onlineSince = $monitor->formattedLastUpdatedStatusChangeDate('forHumans');

            if ($monitor->certificate_check_enabled) {
                $certificateFound = $monitor->certificateStatusAsEmoji;
                $certificateExpirationDate = $monitor->formattedCertificateExpirationDate('forHumans');
                $certificateIssuer = $monitor->certificate_issuer;
            }

            return compact('url', 'reachable', 'onlineSince', 'certificateFound', 'certificateExpirationDate', 'certificateIssuer');
        });

        $titles = ['URL', 'Uptime check', 'Online since', 'Certificate check', 'Certificate Expiration date', 'Certificate Issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
