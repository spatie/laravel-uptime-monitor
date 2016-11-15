<?php

namespace Spatie\UptimeMonitor\Models\Traits;

use Spatie\UptimeMonitor\Events\SiteDown;
use Carbon\Carbon;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

trait SupportsUptimeCheck
{
    public static function bootSupportsUptimeCheck()
    {
        dd('omg it booted');
        static::saving(function (Site $site) {
            if (is_null($site->uptime_status_last_change_date)) {
                $site->uptime_status_last_change_date = Carbon::now();

                return;
            }

            if ($site->getOriginal('uptime_status') != $site->uptime_status) {
                $site->uptime_status_last_change_date = Carbon::now();
            }
        });
    }


    public function shouldCheckUptime() : bool
    {
        if (!$this->enabled) {
            return false;
        }

        if ($this->uptime_status == UptimeStatus::NOT_YET_CHECKED) {
            return true;
        }

        if ($this->uptime_status == UptimeStatus::DOWN) {
            return true;
        }

        if (is_null($this->uptime_last_check_date)) {
            return true;
        }

        return $this->uptime_last_check_date->diffInMinutes() >= $this->uptime_check_interval_in_minutes;
    }

    public function couldReachSite($responseHtml)
    {
        if (!str_contains($responseHtml, $this->look_for_string)) {
            $this->siteIsDown("String `{$this->look_for_string}` was not found on the response.");
        }

        $this->siteIsUp();
    }

    public function couldNotReachSite(string $reason)
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

    protected function shouldFireDownEvent(): bool
    {
        if ($this->uptime_check_times_failed_in_a_row === config('laravel-uptime-monitor.uptime_check.fire_down_event_after_consecutive_failures')) {
            return true;
        }

        if (is_null($this->down_event_fired_on_date)) {
            return false;
        }

        if (config('laravel-uptime-monitor.notifications.resend_down_notification_every_minutes') === 0) {
            return false;
        }

        if ($this->down_event_fired_on_date->diffInMinutes() >= config('laravel-uptime-monitor.notifications.resend_down_notification_every_minutes')) {
            return true;
        }

        return false;
    }
}