<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Helpers\Period;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeCheckRecovered implements ShouldQueue
{
    public Monitor $monitor;

    public Period $downtimePeriod;

    public function __construct(Monitor $monitor, Period $downtimePeriod)
    {
        $this->monitor = $monitor;

        $this->downtimePeriod = $downtimePeriod;
    }
}
