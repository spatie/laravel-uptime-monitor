<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeCheckSucceeded implements ShouldQueue
{
    public Monitor $monitor;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }
}
