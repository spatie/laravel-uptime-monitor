<?php

namespace Spatie\UptimeMonitor\Helpers;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Exceptions\InvalidPeriod;

class Period
{
    /** @var \Carbon\Carbon */
    protected $startDateTime;

    /** @var \Carbon\Carbon */
    protected $endDateTime;

    public function __construct(Carbon $startDateTime, Carbon $endDateTime)
    {
        if ($startDateTime->gt($endDateTime)) {
            throw  InvalidPeriod::startDateMustComeBeforeEndDate($startDateTime, $endDateTime);
        }

        $this->startDateTime = $startDateTime;

        $this->endDateTime = $endDateTime;
    }

    public function duration(): string
    {
        $interval = $this->startDateTime->diff($this->endDateTime);

        return $interval->format('%hh %im');
    }


}