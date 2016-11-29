<?php

namespace Spatie\UptimeMonitor\Checker;

use Spatie\UptimeMonitor\MonitorCollection;

/**
 * Class Checker.
 */
abstract class Checker
{
    /**
     * @var MonitorCollection
     */
    protected $monitors;

    /**
     * @param MonitorCollection $monitors
     * @return mixed
     */
    abstract public function check(MonitorCollection $monitors);
}
