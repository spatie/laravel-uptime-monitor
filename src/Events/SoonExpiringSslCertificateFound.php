<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Site;

class SoonExpiringSslCertificateFound implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    public $site;

    /** @var \Spatie\SslCertificate\SslCertificate */
    public $certificate;

    public function __construct(Site $site, SslCertificate $certificate)
    {
        $this->site = $site;

        $this->certificate = $certificate;
    }
}
