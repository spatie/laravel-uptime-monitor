<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\UptimeMonitor\UptimeMonitorClass
 */
class UptimeMonitorFacade extends Facade
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
