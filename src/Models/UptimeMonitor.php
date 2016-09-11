<?php

namespace Spatie\UptimeMonitor\Models;

use App\Events\SiteDown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use UrlSigner;

class UptimeMonitor extends Model
{
    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_NEVER_CHECKED = 'never checked';

    protected $guarded = [];

    protected $dates = ['last_checked_on'];

    public function shouldRun() : bool
    {
        if (is_null($this->last_checked_on)) {
            return true;
        }

        if ($this->status === self::STATUS_OFFLINE) {
            return true;
        }

        return $this->last_checked_on->diffInMinutes() >= $this->ping_every_minutes;
    }

    public function pingSucceeded($responseHtml)
    {
        $this->status = self::STATUS_ONLINE;
        $this->last_failure_reason = '';

        $wasFailing = $this->times_failed_in_a_row > 0;

        $this->times_failed_in_a_row = 0;
        $this->last_checked_on = Carbon::now();

        $this->save();

        $eventClass = 'App\\Events\\'.($wasFailing ? 'SiteRestored' : 'SiteUp');

        event(new $eventClass($this));
    }

    public function lookForStringPresentOnResponse(string $responseHtml = '') : bool
    {
        if ($this->look_for_string == '') {
            return true;
        }

        return str_contains($responseHtml, $this->look_for_string);
    }

    public function pingFailed(string $reason)
    {
        $this->status = self::STATUS_OFFLINE;

        $this->times_failed_in_a_row++;

        $this->last_checked_on = Carbon::now();

        $this->last_failure_reason = $reason;

        $this->save();

        event(new SiteDown($this));
    }

    public function getCacheKey() : string
    {
        return "{$this->getPingRequestMethod()}:{$this->url}";
    }

    public function getPingRequestMethod() : string
    {
        return $this->look_for_string == '' ? 'HEAD' : 'GET';
    }
}
