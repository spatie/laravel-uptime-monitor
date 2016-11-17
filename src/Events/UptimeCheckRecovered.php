<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeCheckRecovered implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public $monitor;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }
}
