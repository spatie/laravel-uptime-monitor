<?php

namespace Spatie\UptimeMonitor\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Exceptions\CannotSaveMonitor;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Presenters\MonitorPresenter;
use Spatie\UptimeMonitor\Models\Traits\SupportsCertificateCheck;
use Spatie\UptimeMonitor\Models\Traits\SupportsUptimeCheck;
use Spatie\Url\Url;

class Monitor extends Model
{
    use SupportsUptimeCheck;
    use SupportsCertificateCheck;
    use MonitorPresenter;
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['raw_url'];

    protected $casts = [
        'uptime_check_enabled' => 'boolean',
        'certificate_check_enabled' => 'boolean',
        'uptime_last_check_date' => 'datetime',
        'uptime_status_last_change_date' => 'datetime',
        'uptime_check_failed_event_fired_on_date' => 'datetime',
        'certificate_expiration_date' => 'datetime',
    ];

    public function getUptimeCheckAdditionalHeadersAttribute($additionalHeaders): array
    {
        return $additionalHeaders
            ? json_decode($additionalHeaders, true)
            : [];
    }

    public function setUptimeCheckAdditionalHeadersAttribute(array $additionalHeaders): void
    {
        $this->attributes['uptime_check_additional_headers'] = json_encode($additionalHeaders);
    }

    public function scopeEnabled($query)
    {
        return $query
            ->where('uptime_check_enabled', true)
            ->orWhere('certificate_check_enabled', true);
    }

    public function getUrlAttribute(): ?Url
    {
        if (! isset($this->attributes['url'])) {
            return null;
        }

        return Url::fromString($this->attributes['url']);
    }

    public function getRawUrlAttribute(): string
    {
        return (string) $this->url;
    }

    public static function booted()
    {
        static::saving(function (self $monitor) {
            if (static::alreadyExists($monitor)) {
                throw CannotSaveMonitor::alreadyExists($monitor);
            }
        });
    }

    public function isHealthy(): bool
    {
        if ($this->uptime_check_enabled && in_array($this->uptime_status, [UptimeStatus::DOWN, UptimeStatus::NOT_YET_CHECKED])) {
            return false;
        }

        if ($this->certificate_check_enabled && $this->certificate_status === CertificateStatus::INVALID) {
            return false;
        }

        return true;
    }

    public function enable(): self
    {
        $this->uptime_check_enabled = true;

        if ($this->url->getScheme() === 'https') {
            $this->certificate_check_enabled = true;
        }

        $this->save();

        return $this;
    }

    public function disable(): self
    {
        $this->uptime_check_enabled = false;
        $this->certificate_check_enabled = false;

        $this->save();

        return $this;
    }

    protected static function alreadyExists(self $monitor): bool
    {
        $query = static::where('url', $monitor->url);

        if ($monitor->exists) {
            $query->where('id', '<>', $monitor->id);
        }

        return (bool) $query->first();
    }
}
