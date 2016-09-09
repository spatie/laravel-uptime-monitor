<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\UptimeMonitor\UptimeMonitorClass
 */
class SkeletonFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'UptimeMonitor';
    }
}
