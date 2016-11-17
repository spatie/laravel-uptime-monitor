<?php

namespace Spatie\UptimeMonitor\Models\Presenters;

use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

trait MonitorPresenter
{
    public function getReachableAsEmojiAttribute(): string
    {
        if ($this->uptime_status === UptimeStatus::UP) {
            return Emoji::ok();
        }

        if ($this->uptime_status === UptimeStatus::DOWN) {
            return Emoji::notOk();
        }

        return '';
    }

    public function getSslCertificateStatusAsEmojiAttribute(): string
    {
        if ($this->ssl_certificate_status === SslCertificateStatus::VALID) {
            return Emoji::ok();
        }

        if ($this->ssl_certificate_status === SslCertificateStatus::INVALID) {
            return Emoji::notOk();
        }

        return '';
    }

    public function getFormattedLastUpdatedStatusChangeDateAttribute(): string
    {
        return $this->formatDate('uptime_status_last_change_date');
    }

    public function getFormattedSslCertificateExpirationDateAttribute(): string
    {
        return $this->formatDate('ssl_certificate_expiration_date');
    }

    public function getChunkedLastFailureReasonAttribute(): string
    {
        if ($this->uptime_failure_reason == '') {
            return '';
        }

        return chunk_split($this->uptime_failure_reason, 30, "\n");
    }

    public function getChunkedLastSslFailureReasonAttribute(): string
    {
        if ($this->ssl_certificate_failure_reason == '') {
            return '';
        }

        return chunk_split($this->ssl_certificate_failure_reason, 60, "\n");
    }

    protected function formatDate(string $attributeName): string
    {
        if (! $this->$attributeName) {
            return '';
        }

        return $this->$attributeName->diffForHumans();
    }
}
