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
        $this->ssl_certificate_status = $certificate->isValid($this->url)
            ? SslCertificateStatus::VALID
            : SslCertificateStatus::INVALID;

        $this->ssl_certificate_expiration_date = $certificate->expirationDate();
        $this->ssl_certificate_issuer = $certificate->getIssuer();
        $this->save();

        $this->fireEventsForUpdatedMonitorWithCertificate($this, $certificate);
    }

    public function updateWithCertificateException(Exception $exception)
    {
        $this->ssl_certificate_status = SslCertificateStatus::INVALID;
        $this->ssl_certificate_expiration_date = null;
        $this->ssl_certificate_issuer = '';
        $this->ssl_certificate_failure_reason = $exception->getMessage();
        $this->save();

        event(new SslCheckFailed($this, $exception->getMessage()));
    }

    protected function fireEventsForUpdatedMonitorWithCertificate(Monitor $monitor, SslCertificate $certificate)
    {
        if ($this->ssl_certificate_status === SslCertificateStatus::VALID) {
            event(new SslCheckSucceeded($this, $certificate));

            if ($certificate->expirationDate()->diffInDays() <= config('laravel-uptime-monitor.notifications.send_notification_when_ssl_certificate_will_expire_in_days')) {
                event(new SslExpiresSoon($monitor, $certificate));
            }

            return;
        }

        if ($this->ssl_certificate_status === SslCertificateStatus::INVALID) {
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
