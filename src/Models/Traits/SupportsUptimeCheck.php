<?php

namespace Spatie\UptimeMonitor\Models\Traits;

use Spatie\UptimeMonitor\Events\SiteDown;
use Carbon\Carbon;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

trait SupportsUptimeCheck
{
    public function shouldCheckUptime() : bool
    {
        if (!$this->enabled) {
            return false;
        }

        if ($this->uptime_status = UptimeStatus::NOT_YET_CHECKED) {
            return true;
        }

        if ($this->uptime_status === UptimeStatus::DOWN) {
            return true;
        }

        return $this->uptime_last_check_date->diffInMinutes() >= $this->ping_every_minutes;
    }

    public function pingSucceeded($responseHtml)
    {
        if (!$this->lookForStringPresentOnResponse($responseHtml)) {
            $this->siteIsDown("String `{$this->look_for_string}` was not found on the response.");
        }

        $this->siteIsUp();
    }

    public function pingFailed(string $reason)
    {
        $this->siteIsDown($reason);
    }

    public function siteIsUp()
    {
        $this->uptime_status = UptimeStatus::UP;
        $this->uptime_failure_reason = '';

        $wasFailing = !is_null($this->down_event_fired_on_date);

        $this->uptime_check_times_failed_in_a_row = 0;
        $this->uptime_last_check_date = Carbon::now();
        $this->down_event_fired_on_date = null;

        $this->save();

        $eventClass = ($wasFailing ? SiteRestored::class : SiteUp::class);

        event(new $eventClass($this));
    }

    public function siteIsDown(string $reason)
    {
        $this->uptime_status = UptimeStatus::DOWN;

        $this->uptime_check_times_failed_in_a_row++;

        $this->uptime_last_check_date = Carbon::now();

        $this->uptime_failure_reason = $reason;

        $this->save();

        if ($this->shouldFireDownEvent()) {
            $this->down_event_fired_on_date = Carbon::now();
            $this->save();

            event(new SiteDown($this));
        }
    }

    public function lookForStringPresentOnResponse(string $responseHtml = '') : bool
    {
        if ($this->look_for_string == '') {
            return true;
        }

        return str_contains($responseHtml, $this->look_for_string);
    }

    protected function shouldFireDownEvent(): bool
    {
        if ($this->uptime_check_times_failed_in_a_row === config('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures')) {
            return true;
        }

        if (is_null($this->down_event_fired_on_date)) {
            return false;
        }

        if (config('laravel-uptime-monitor.notifications.resend_down_notification_every_minutes') == 0) {
            return false;
        }

        if ($this->down_event_fired_on_date->diffInMinutes() >= config('laravel-uptime-monitor.notifications.resend_down_notification_every_minutes')) {
            return true;
        }

        return false;
    }
}