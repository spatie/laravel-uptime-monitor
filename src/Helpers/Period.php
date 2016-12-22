<?php

namespace Spatie\UptimeMonitor\Helpers;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Exceptions\InvalidPeriod;

class Period
{
    /** @var \Carbon\Carbon */
    public $startDateTime;

    /** @var \Carbon\Carbon */
    public $endDateTime;

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

        if ($interval->format('%h') === '0') {
            return $interval->format('%im');
        }

        return $interval->format('%hh %im');
    }

    public function toText(): string
    {
        $configuredDateFormat = config('laravel-uptime-monitor.notifications.date_format');

        return
            $this->startDateTime->format('H:i').' '
            .($this->startDateTime->isToday() ? '' : "on {$this->startDateTime->format($configuredDateFormat)} ")
            .Emoji::rightwardsArrow().' '
            .$this->endDateTime->format('H:i')
            .($this->endDateTime->isToday() ? '' : " on {$this->endDateTime->format($configuredDateFormat)}");
    }
}
