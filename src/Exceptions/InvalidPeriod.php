<?php

namespace Spatie\UptimeMonitor\Exceptions;

use Carbon\CarbonInterface;
use Exception;

class InvalidPeriod extends Exception
{
    public static function startDateMustComeBeforeEndDate(CarbonInterface $startDateTime, CarbonInterface $endDateTime): self
    {
        return new static("The given startDateTime `{$startDateTime->toIso8601String()}` is not before `{$endDateTime->toIso8601String()}`");
    }
}
