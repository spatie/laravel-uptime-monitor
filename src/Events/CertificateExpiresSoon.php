<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Monitor;

class CertificateExpiresSoon implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public $monitor;

    /** @var \Spatie\SslCertificate\SslCertificate */
    public $certificate;

    public function __construct(Monitor $monitor, SslCertificate $certificate)
    {
        $this->monitor = $monitor;

        $this->certificate = $certificate;
    }
}
