<?php

namespace Spatie\UptimeMonitor\Exceptions;

use Carbon\Carbon;
use Exception;

class InvalidPeriod extends Exception
{
    public static function startDateMustComeBeforeEndDate(Carbon $startDateTime, Carbon $endDateTime)
    {
        return new static("The given startDateTime `{$startDateTime->toIso8601String()}` is not before `{$endDateTime->toIso8601String()}`");
    }
}
