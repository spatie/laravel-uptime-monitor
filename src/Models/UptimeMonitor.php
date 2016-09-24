<?php

namespace Spatie\UptimeMonitor\Models;

use Spatie\UptimeMonitor\Events\SiteDown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Events\SiteUp;
use UrlSigner;

class UptimeMonitor extends Model
{
    const STATUS_UP = 'online';
    const STATUS_DOWN = 'offline';
    const STATUS_NEVER_CHECKED = 'never checked';

    protected $guarded = [];

    protected $dates = [
        'last_checked_on',
        'last_status_change_on',
        'ssl_certificate_expiration_date',
    ];

    public static function boot()
    {
        static::saving(function (UptimeMonitor $uptimeMonitor) {
            if ($uptimeMonitor->getOriginal('status') != $uptimeMonitor->status) {
                $uptimeMonitor->last_status_change_on = Carbon::now();
            };
        });
    }

    public function shouldCheck() : bool
    {
        if (! $this->enabled) {
            return false;
        }

        if ($this->status = static::STATUS_NEVER_CHECKED) {
            return true;
        }

        if ($this->status === static::STATUS_DOWN) {
            return true;
        }

        return $this->last_checked_on->diffInMinutes() >= $this->ping_every_minutes;
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
        $this->status = self::STATUS_UP;
        $this->last_failure_reason = '';

        $wasFailing = $this->times_failed_in_a_row > 0;

        $this->times_failed_in_a_row = 0;
        $this->last_checked_on = Carbon::now();

        $this->save();

        $eventClass = ($wasFailing ? SiteRestored::class : SiteUp::class);

        event(new $eventClass($this));
    }

    public function siteIsDown(string $reason)
    {
        $previousStatus = $this->status;

        $this->status = static::STATUS_DOWN;

        $this->times_failed_in_a_row++;

        $this->last_checked_on = Carbon::now();

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
        if ($previousStatus != static::STATUS_DOWN) {
            return true;
        }

        if (Carbon::now()->diffInMinutes() >= config('resend_down_notification_every_minutes')) {
            return true;
        }

        return false;
    }
}
