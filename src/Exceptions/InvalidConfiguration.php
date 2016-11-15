<?php

namespace Spatie\UptimeMonitor\Exceptions;

use Exception;
use Spatie\UptimeMonitor\Models\Site;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className)
    {
        return new static("The given model class `$className` does not extend `".Site::class.'`');
    }
}
