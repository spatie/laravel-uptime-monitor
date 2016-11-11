<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Models\Site;

class InvalidSslCertificateFound implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    public $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }
}
