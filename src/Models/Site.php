<?php

namespace Spatie\UptimeMonitor\Models;

use Spatie\UptimeMonitor\Events\SiteDown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\Url\Url;
use UrlSigner;

class Site extends Model
{
    protected $guarded = [];

    protected $dates = [
        'uptime_last_checked_on',
        'last_uptime_status_change_on',
        'ssl_certificate_expiration_date',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function getUrlAttribute()
    {
        return Url::fromString($this->attributes['url']);
    }

    public static function boot()
    {
        static::saving(function (Site $site) {
            if ($site->getOriginal('status') != $site->status) {
                $site->last_uptime_status_change_on = Carbon::now();
            };
        });
    }

    public function shouldCheckUptime() : bool
    {
        if (! $this->enabled) {
            return false;
        }

        if ($this->status = UptimeStatus::NOT_YET_CHECKED) {
            return true;
        }

        if ($this->status === UptimeStatus::DOWN) {
            return true;
        }

        return $this->uptime_last_checked_on->diffInMinutes() >= $this->ping_every_minutes;
    }

    public function pingSucceeded($responseHtml)
    {
        if (!$this->lookForStringPresentOnResponse($responseHtml)) {
            $this->siteIsDown("String `{$this->look_for_string}` was not found on the response");
        }

        $this->siteIsUp();
    }

    public function pingFailed(string $reason)
    {
        $this->siteIsDown($reason);
    }

    public function siteIsUp()
    {
        $this->status = UptimeStatus::UP;
        $this->last_failure_reason = '';

        $wasFailing = $this->times_failed_in_a_row > 0;

        $this->times_failed_in_a_row = 0;
        $this->uptime_last_checked_on = Carbon::now();

        $this->save();

        $eventClass = ($wasFailing ? SiteRestored::class : SiteUp::class);

        event(new $eventClass($this));
    }

    public function siteIsDown(string $reason)
    {
        $previousStatus = $this->status;

        $this->status = UptimeStatus::DOWN;

        $this->times_failed_in_a_row++;

        $this->uptime_last_checked_on = Carbon::now();

        $this->last_failure_reason = $reason;

        $this->save();

        if ($this->shouldFireDownEvent($previousStatus)) {

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

    public function getPingRequestMethod() : string
    {
        return $this->look_for_string == '' ? 'HEAD' : 'GET';
    }

    protected function shouldFireDownEvent($previousStatus): bool
    {
        if ($previousStatus != UptimeStatus::DOWN) {
            return true;
        }

        if (Carbon::now()->diffInMinutes() >= config('resend_down_notification_every_minutes')) {
            return true;
        }

        return false;
    }
}
