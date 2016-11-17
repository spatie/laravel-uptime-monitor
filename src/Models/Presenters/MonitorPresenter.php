<?php

namespace Spatie\UptimeMonitor\Models\Presenters;

use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
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
        if ($this->certificate_status === CertificateStatus::VALID) {
            return Emoji::ok();
        }

        if ($this->certificate_status === CertificateStatus::INVALID) {
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
        return $this->formatDate('certificate_expiration_date');
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
        if ($this->certificate_failure_reason == '') {
            return '';
        }

        return chunk_split($this->certificate_failure_reason, 60, "\n");
    }

    protected function formatDate(string $attributeName): string
    {
        if (! $this->$attributeName) {
            return '';
        }

        return $this->$attributeName->diffForHumans();
    }
}
