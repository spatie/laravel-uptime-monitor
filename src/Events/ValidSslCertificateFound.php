<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Monitor;

class ValidSslCertificateFound implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public $monitor;

    /** @var \Spatie\SslCertificate\SslCertificate */
    public $certificate;

    public function __construct(Monitor $monitor, SslCertificate $certificate)
    {
        $this->site = $monitor;

        $this->certificate = $certificate;
    }
}
