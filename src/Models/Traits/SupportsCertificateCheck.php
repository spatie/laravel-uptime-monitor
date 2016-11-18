<?php

namespace Spatie\UptimeMonitor\Models\Traits;

use Exception;
use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed;
use Spatie\UptimeMonitor\Events\CertificateExpiresSoon;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Monitor;

trait SupportsCertificateCheck
{
    public function checkCertificate()
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
            ? CertificateStatus::VALID
            : CertificateStatus::INVALID;

        $this->certificate_expiration_date = $certificate->expirationDate();
        $this->certificate_issuer = $certificate->getIssuer();
        $this->save();

        $this->fireEventsForUpdatedMonitorWithCertificate($this, $certificate);
    }

    public function updateWithCertificateException(Exception $exception)
    {
        $this->certificate_status = CertificateStatus::INVALID;
        $this->certificate_expiration_date = null;
        $this->certificate_issuer = '';
        $this->certificate_check_failure_reason = $exception->getMessage();
        $this->save();

        event(new CertificateCheckFailed($this, $exception->getMessage()));
    }

    protected function fireEventsForUpdatedMonitorWithCertificate(Monitor $monitor, SslCertificate $certificate)
    {
        if ($this->certificate_status === CertificateStatus::VALID) {
            event(new CertificateCheckSucceeded($this, $certificate));

            if ($certificate->expirationDate()->diffInDays() <= config('laravel-uptime-monitor.certificate_check.fire_expiring_soon_event_if_certificate_expires_within_days')) {
                event(new CertificateExpiresSoon($monitor, $certificate));
            }

            return;
        }

        if ($this->certificate_status === CertificateStatus::INVALID) {
            $reason = 'Unknown reason';

            if ($certificate->appliesToUrl($this->url)) {
                $reason = "Certificate does not apply to {$this->url} but only to these domains: ".implode(',', $certificate->getAdditionalDomains());
            }

            if ($certificate->isExpired()) {
                $reason = 'The certificate is expired';
            }

            event(new CertificateCheckFailed($this, $reason, $certificate));
        }
    }
}
