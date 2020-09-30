<?php

namespace Spatie\UptimeMonitor\Exceptions;

use Exception;
use Spatie\UptimeMonitor\Models\Monitor;

class CannotSaveMonitor extends Exception
{
    public static function alreadyExists(Monitor $monitor): self
    {
        return new static("Could not save a monitor for url `{$monitor->url}` because there already exists another monitor with the same url. ".
            'Try saving a monitor with a different url.');
    }
}
