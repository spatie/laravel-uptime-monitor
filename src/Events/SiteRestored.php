<?php

namespace Spatie\UptimeMonitor\Events;

class SiteRestored
{
    /** @var \Spatie\UptimeMonitor\Models\UptimeMonitor */
    public $uptimeMonitor;

    public function __construct(UptimeMonitor $uptimeMonitor)
    {
        $this->uptimeMonitor = $uptimeMonitor;
    }
}
