<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Monitor;

class CertificateCheckFailed implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public Monitor $monitor;

    /** @var string */
    public string $reason;

    public ?SslCertificate $certificate;

    public function __construct(Monitor $monitor, string $reason, SslCertificate $certificate = null)
    {
        $this->monitor = $monitor;

        $this->reason = $reason;

        $this->certificate = $certificate;
    }
}
