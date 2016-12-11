<?php

namespace Spatie\UptimeMonitor\Events;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\SslCertificate\SslCertificate;
use Illuminate\Contracts\Queue\ShouldQueue;

class CertificateCheckSucceeded implements ShouldQueue
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
