<?php

namespace Spatie\UptimeMonitor\Models\Traits;

use Exception;
use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Events\SslCheckFailed;
use Spatie\UptimeMonitor\Events\SslExpiresSoon;
use Spatie\UptimeMonitor\Events\SslCheckSucceeded;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Monitor;

trait SupportsSslCertificateCheck
{
    public function checkSslCertificate()
    {
        try {
            $certificate = SslCertificate::createForHostName($this->url->getHost());

            $this->updateWithCertificate($certificate);
        } catch (CouldNotDownloadCertificate $exception) {
            $this->updateWithCertificateException($exception);
        }
    }

    public function updateWithCertificate(SslCertificate $certificate)
    {
        $this->certificate_status = $certificate->isValid($this->url)
            ? SslCertificateStatus::VALID
            : SslCertificateStatus::INVALID;

        $this->certificate_expiration_date = $certificate->expirationDate();
        $this->certificate_issuer = $certificate->getIssuer();
        $this->save();

        $this->fireEventsForUpdatedMonitorWithCertificate($this, $certificate);
    }

    public function updateWithCertificateException(Exception $exception)
    {
        $this->certificate_status = SslCertificateStatus::INVALID;
        $this->certificate_expiration_date = null;
        $this->certificate_issuer = '';
        $this->certificate_failure_reason = $exception->getMessage();
        $this->save();

        event(new SslCheckFailed($this, $exception->getMessage()));
    }

    protected function fireEventsForUpdatedMonitorWithCertificate(Monitor $monitor, SslCertificate $certificate)
    {
        if ($this->certificate_status === SslCertificateStatus::VALID) {
            event(new SslCheckSucceeded($this, $certificate));

            if ($certificate->expirationDate()->diffInDays() <= config('laravel-uptime-monitor.ssl-check.fire_expiring_soon_event_if_certificate_expires_within_days')) {
                event(new SslExpiresSoon($monitor, $certificate));
            }

            return;
        }

        if ($this->certificate_status === SslCertificateStatus::INVALID) {
            $reason = 'Unknown reason';

            if ($certificate->appliesToUrl($this->url)) {
                $reason = "Certificate does not apply to {$this->url} but only to these domains: ".implode(',', $certificate->getAdditionalDomains());
            }

            if ($certificate->isExpired()) {
                $reason = 'The certificate is expired';
            }

            event(new SslCheckFailed($this, $reason, $certificate));
        }
    }
}
