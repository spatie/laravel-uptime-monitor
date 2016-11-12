<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Models\Site;

class InvalidSslCertificateFound implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    public $site;

    /** @var string */
    public $reason;

    /** @var \Spatie\SslCertificate\SslCertificate|null */
    public $certificate;

    public function __construct(Site $site, string $reason, SslCertificate $certificate = null)
    {
        $this->site = $site;

        $this->reason = $reason;

        $this->certificate = $certificate;
    }
}
