<?php

namespace Spatie\UptimeMonitor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Helpers\Period;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeCheckRecovered implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public $monitor;

    /** @var \Spatie\UptimeMonitor\Helpers\Period */
    public $downtimePeriod;

    public function __construct(Monitor $monitor, Period $downtimePeriod)
    {
        $this->monitor = $monitor;

        $this->downtimePeriod = $downtimePeriod;
    }
}
