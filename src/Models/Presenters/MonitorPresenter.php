<?php

namespace Spatie\UptimeMonitor\Models\Presenters;

use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

trait MonitorPresenter
{
    public function getUptimeStatusAsEmojiAttribute(): string
    {
        if ($this->uptime_status === UptimeStatus::UP) {
            return Emoji::ok();
        }

        if ($this->uptime_status === UptimeStatus::DOWN) {
            return Emoji::notOk();
        }

        return '';
    }

    public function getCertificateStatusAsEmojiAttribute(): string
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

    public function getFormattedCertificateExpirationDateAttribute(): string
    {
        return $this->formatDate('certificate_expiration_date');
    }

    public function getChunkedLastFailureReasonAttribute(): string
    {
        if ($this->uptime_check_failure_reason == '') {
            return '';
        }

        return chunk_split($this->uptime_check_failure_reason, 30, "\n");
    }

    public function getChunkedLastCertificateCheckFailureReasonAttribute(): string
    {
        if ($this->certificate_check_failure_reason == '') {
            return '';
        }

        return chunk_split($this->certificate_check_failure_reason, 60, "\n");
    }

    protected function formatDate(string $attributeName): string
    {
        if (! $this->$attributeName) {
            return '';
        }

        return $this->$attributeName->diffForHumans();
    }
}
