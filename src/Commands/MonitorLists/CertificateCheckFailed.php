<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class CertificateCheckFailed
{
    public static function display()
    {
        $monitorsWithFailingCertificateChecks = MonitorRepository::getWithFailingCertificateCheck();

        if (! $monitorsWithFailingCertificateChecks->count()) {
            return;
        }

        ConsoleOutput::warn('Certificate check failed');
        ConsoleOutput::warn('========================');

        $rows = $monitorsWithFailingCertificateChecks->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reason = $monitor->chunkedLastCertificateCheckFailureReason;

            return compact('url', 'reason');
        });

        $titles = ['URL', 'Problem description'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
