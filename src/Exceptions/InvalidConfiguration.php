<?php

namespace Spatie\UptimeMonitor\Exceptions;

use Exception;
use Spatie\UptimeMonitor\Models\Monitor;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className): self
    {
        return new static("The given model class `{$className}` does not extend `".Monitor::class.'`');
    }
}
