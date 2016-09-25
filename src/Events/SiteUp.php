<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Models\Site;

class SiteUp implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    public $site;

    public function __construct(Site $site)
    {
        $this->uptimeMonitor = $site;
    }
}
