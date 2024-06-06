<?php

namespace Spatie\UptimeMonitor\Test\Integration\Models\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Event;
use Spatie\SslCertificate\Downloader;
use Spatie\SslCertificate\SslCertificate;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded;
use Spatie\UptimeMonitor\Events\CertificateExpiresSoon;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class SupportsCertificateCheckTest extends TestCase
{
    protected Monitor $monitor;
    protected int $daysBeforeExpiration;
    protected SslCertificate $certificate;
    protected const DOMAIN = 'https://google.com';

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->monitor = Monitor::factory()->create([
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::NOT_YET_CHECKED,
            'url' => self::DOMAIN
        ]);

        $this->certificate = Downloader::downloadCertificateFromUrl(self::DOMAIN);
        $this->daysBeforeExpiration = config(
            'uptime-monitor.certificate_check.fire_expiring_soon_event_if_certificate_expires_within_days'
        );
    }

    /** @test */
    public function it_can_set_valid_certificate_not_within_expiration_range()
    {
        // Collect
        Carbon::setTestNow($this->certificate->expirationDate()->subDays($this->daysBeforeExpiration + 1));

        // Act
        $this->monitor->setCertificate($this->certificate);

        // Assert
        $this->monitor->fresh();
        $this->assertSame(CertificateStatus::VALID, $this->monitor->certificate_status);
        $this->assertSame($this->certificate->getIssuer(), $this->monitor->certificate_issuer);
        $this->assertTrue($this->monitor->certificate_expiration_date->isSameDay($this->certificate->expirationDate()));
        Event::assertDispatched(CertificateCheckSucceeded::class);
        Event::assertNotDispatched(CertificateCheckFailed::class);
        Event::assertNotDispatched(CertificateExpiresSoon::class);
    }

    /** @test */
    public function it_can_set_valid_certificate_within_expiration_range()
    {
        // Collect
        Carbon::setTestNow($this->certificate->expirationDate()->subDays($this->daysBeforeExpiration - 1));

        // Act
        $this->monitor->setCertificate($this->certificate);

        // Assert
        $this->monitor->fresh();
        $this->assertSame(CertificateStatus::VALID, $this->monitor->certificate_status);
        $this->assertSame($this->certificate->getIssuer(), $this->monitor->certificate_issuer);
        $this->assertTrue($this->monitor->certificate_expiration_date->isSameDay($this->certificate->expirationDate()));
        Event::assertDispatched(CertificateCheckSucceeded::class);
        Event::assertDispatched(CertificateExpiresSoon::class);
        Event::assertNotDispatched(CertificateCheckFailed::class);
    }

    /** @test */
    public function it_can_set_invalid_certificate()
    {
        // Collect
        Carbon::setTestNow($this->certificate->expirationDate()->addDay());

        // Act
        $this->monitor->setCertificate($this->certificate);

        // Assert
        $this->monitor->fresh();
        $this->assertSame(CertificateStatus::INVALID, $this->monitor->certificate_status);
        $this->assertSame('The certificate has expired', $this->monitor->certificate_check_failure_reason);
        $this->assertSame($this->certificate->getIssuer(), $this->monitor->certificate_issuer);
        $this->assertTrue($this->monitor->certificate_expiration_date->isSameDay($this->certificate->expirationDate()));
        Event::assertDispatched(CertificateCheckFailed::class);
        Event::assertNotDispatched(CertificateCheckSucceeded::class);
        Event::assertNotDispatched(CertificateExpiresSoon::class);
    }

    /** @test */
    public function it_can_set_certificate_exception()
    {
        // Collect
        $exception = new Exception('Certificate check failed');

        // Act
        $this->monitor->setCertificateException($exception);

        // Assert
        $this->monitor->fresh();
        $this->assertSame(CertificateStatus::INVALID, $this->monitor->certificate_status);
        $this->assertSame('Certificate check failed', $this->monitor->certificate_check_failure_reason);
        $this->assertSame('', $this->monitor->certificate_issuer);
        $this->assertNull($this->monitor->certificate_expiration_date);
        Event::assertDispatched(CertificateCheckFailed::class);
    }
}
