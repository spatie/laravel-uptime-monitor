<?php

namespace Spatie\UptimeMonitor\Models;

use Exception;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound;
use Spatie\UptimeMonitor\Events\SiteDown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Events\SiteRestored;
use Spatie\UptimeMonitor\Events\SiteUp;
use Spatie\UptimeMonitor\Events\SoonExpiringSslCertificateFound;
use Spatie\UptimeMonitor\Events\ValidSslCertificateFound;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Presenters\SitePresenter;
use Spatie\Url\Url;

class Site extends Model
{
    use SitePresenter;

    protected $guarded = [];

    protected $dates = [
        'uptime_last_check_date',
        'uptime_status_last_change_date',
        'down_event_fired_on_date',
        'ssl_certificate_expiration_date',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'check_ssl_certificate' => 'boolean',
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
        if (! $this->enabled) {
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

        if (! $this->lookForStringPresentOnResponse($responseHtml)) {
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

        $this->uptime_status = UptimeStatus::UP;
        $this->uptime_failure_reason = '';

        $wasFailing = ! is_null($this->down_event_fired_on_date);

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
        if ($this->uptime_check_times_failed_in_a_row === config('laravel-uptime-monitor.fire_down_event_after_consecutive_failed_checks')) {
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

    public function isHealthy()
    {
        if (in_array($this->uptime_status, [UptimeStatus::DOWN, UptimeStatus::NOT_YET_CHECKED])) {
            return false;
        }

        if ($this->check_ssl_certificate && $this->ssl_certificate_status === SslCertificateStatus::INVALID) {
            return false;
        }

        return true;
    }

    public function updateWithCertificate(SslCertificate $certificate)
    {
        $this->ssl_certificate_status = $certificate->isValid($this->url)
            ? SslCertificateStatus::VALID
            : SslCertificateStatus::INVALID;

        $this->ssl_certificate_expiration_date = $certificate->expirationDate();

        $this->ssl_certificate_issuer = $certificate->getIssuer();

        $this->save();

        $this->fireEventsForUpdatedSiteWithCertificate($this, $certificate);
    }

    public function updateWithCertificateException(Exception $exception)
    {
        $this->ssl_certificate_status = SslCertificateStatus::INVALID;
        $this->ssl_certificate_expiration_date = null;
        $this->ssl_certificate_issuer = '';
        $this->ssl_certificate_failure_reason = $exception->getMessage();

        $this->save();

        event(new InvalidSslCertificateFound($this, $exception->getMessage()));
    }

    protected function fireEventsForUpdatedSiteWithCertificate(Site $site, SslCertificate $certificate)
    {
        if ($this->ssl_certificate_status === SslCertificateStatus::VALID) {
            event(new ValidSslCertificateFound($this, $certificate));

            if ($certificate->expirationDate()->diffInDays() <= config('laravel-uptime-monitor.notifications.send_notification_when_ssl_certificate_will_expire_in_days')) {
                event(new SoonExpiringSslCertificateFound($site, $certificate));
            }

            return;
        }

        if ($this->ssl_certificate_status === SslCertificateStatus::INVALID) {

            $reason = 'Unknown reason';

            if ($certificate->appliesToUrl($this->url)) {
                $reason = "Certificate does not apply to {$this->url} but only to these domains: " . implode(',', $certificate->getAdditionalDomains());
            }

            if ($certificate->isExpired()) {
                $reason = "The certificate is expired";
            }

            event(new InvalidSslCertificateFound($this, $reason, $certificate));
        }
    }
}
