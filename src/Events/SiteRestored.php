<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Models\UptimeMonitor;

class SiteRestored implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\UptimeMonitor */
    public $uptimeMonitor;

    public function __construct(UptimeMonitor $uptimeMonitor)
    {
        $this->uptimeMonitor = $uptimeMonitor;
    }
}
