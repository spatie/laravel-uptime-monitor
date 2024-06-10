<?php

namespace Spatie\UptimeMonitor\Helpers;

use Carbon\CarbonInterface;
use Spatie\UptimeMonitor\Exceptions\InvalidPeriod;

class Period
{
    public CarbonInterface $startDateTime;

    public CarbonInterface $endDateTime;

    public function __construct(CarbonInterface $startDateTime, CarbonInterface $endDateTime)
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

        if ($this->startDateTime->diffInHours($this->endDateTime) < 1) {
            return $interval->format('%im');
        }

        if ($this->startDateTime->diffInDays($this->endDateTime) < 1) {
            return $interval->format('%hh %im');
        }

        return $interval->format('%dd %hh %im');
    }

    public function toText(): string
    {
        $configuredDateFormat = config('uptime-monitor.notifications.date_format');

        return
            $this->startDateTime->format('H:i').' '
            .($this->startDateTime->isToday() ? '' : "on {$this->startDateTime->format($configuredDateFormat)} ")
            .'➡️ '
            .$this->endDateTime->format('H:i')
            .($this->endDateTime->isToday() ? '' : " on {$this->endDateTime->format($configuredDateFormat)}");
    }
}
