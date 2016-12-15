<?php

namespace Spatie\UptimeMonitor\Events;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\SslCertificate\SslCertificate;
use Illuminate\Contracts\Queue\ShouldQueue;

class CertificateCheckFailed implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public $monitor;

    /** @var string */
    public $reason;

    /** @var \Spatie\SslCertificate\SslCertificate|null */
    public $certificate;

    public function __construct(Monitor $monitor, string $reason, SslCertificate $certificate = null)
    {
        $this->monitor = $monitor;

        $this->reason = $reason;

        $this->certificate = $certificate;
    }
}
