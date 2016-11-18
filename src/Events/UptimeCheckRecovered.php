<?php

namespace Spatie\UptimeMonitor\Events;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeCheckRecovered implements ShouldQueue
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    public $monitor;

    /**
     * @var \Spatie\UptimeMonitor\Events\Carbon
     */
    protected $uptimeCheckStartedFailingOnDate;

    public function __construct(Monitor $monitor, Carbon $uptimeCheckStartedFailingOnDate = null)
    {
        $this->monitor = $monitor;

        $this->uptimeCheckStartedFailingOnDate = $uptimeCheckStartedFailingOnDate;
    }
}
