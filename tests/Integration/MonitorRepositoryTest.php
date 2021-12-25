<?php

namespace Spatie\UptimeMonitor\Test\Integration;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\Url\Url;

class MonitorRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_get_all_enabled_monitors()
    {
        Monitor::create(['url' => 'http://enabled1.com', 'uptime_check_enabled' => true]);

        Monitor::create(['url' => 'http://disabled1.com', 'uptime_check_enabled' => false]);

        Monitor::create(['url' => 'http://enabled2.com', 'uptime_check_enabled' => true]);

        Monitor::create(['url' => 'http://disabled2.com', 'uptime_check_enabled' => false]);

        $enabledMonitors = MonitorRepository::getEnabled();

        $this->assertEquals(['http://enabled1.com', 'http://enabled2.com'], $this->getMonitorUrls($enabledMonitors));
    }

    /** @test */
    public function it_can_get_all_disabled_monitors()
    {
        Monitor::create(['url' => 'http://enabled1.com', 'uptime_check_enabled' => true]);

        Monitor::create(['url' => 'http://disabled1.com', 'uptime_check_enabled' => false]);

        Monitor::create(['url' => 'http://enabled2.com', 'uptime_check_enabled' => true]);

        Monitor::create(['url' => 'http://disabled2.com', 'uptime_check_enabled' => false]);

        $disabledMonitors = MonitorRepository::getDisabled();

        $this->assertEquals(['http://disabled1.com', 'http://disabled2.com'], $this->getMonitorUrls($disabledMonitors));
    }

    /** @test */
    public function it_can_get_all_unchecked_monitors()
    {
        Monitor::create(['url' => 'http://down1.com', 'uptime_status' => UptimeStatus::DOWN]);

        Monitor::create(['url' => 'http://up.com', 'uptime_status' => UptimeStatus::UP]);

        Monitor::create([
            'url' => 'http://checked.com',
            'uptime_status' => UptimeStatus::UP,
            'certificate_status' => CertificateStatus::VALID,
        ]);

        Monitor::create([
            'url' => 'http://unchecked1.com',
            'uptime_status' => UptimeStatus::UP,
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::NOT_YET_CHECKED,
        ]);

        Monitor::create([
            'url' => 'http://unchecked2.com',
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::NOT_YET_CHECKED,
        ]);

        Monitor::create([
            'url' => 'http://disabled1.com',
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'uptime_check_enabled' => false,
            'certificate_status' => CertificateStatus::NOT_YET_CHECKED,
        ]);

        Monitor::create([
            'url' => 'http://enabled.com',
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'uptime_check_enabled' => false,
            'certificate_status' => CertificateStatus::NOT_YET_CHECKED,
            'certificate_check_enabled' => true,
        ]);

        $uncheckedMonitors = MonitorRepository::getUnchecked();
        $this->assertEquals(['http://enabled.com', 'http://unchecked1.com', 'http://unchecked2.com'], $this->getMonitorUrls($uncheckedMonitors));
    }

    /** @test */
    public function it_can_get_all_monitors_that_are_failing()
    {
        Monitor::create(['url' => 'http://down1.com', 'uptime_status' => UptimeStatus::DOWN]);

        Monitor::create(['url' => 'http://up.com', 'uptime_status' => UptimeStatus::UP]);

        Monitor::create(['url' => 'http://down2.com', 'uptime_status' => UptimeStatus::DOWN]);

        $failingMonitors = MonitorRepository::getWithFailingUptimeCheck();

        $this->assertEquals(['http://down1.com', 'http://down2.com'], $this->getMonitorUrls($failingMonitors));
    }

    /** @test */
    public function it_can_get_all_monitors_that_need_an_certificate_check()
    {
        Monitor::create(['url' => 'http://site1.com', 'uptime_check_enabled' => false, 'certificate_check_enabled' => false]);

        Monitor::create(['url' => 'http://site2.com', 'uptime_check_enabled' => false, 'certificate_check_enabled' => true]);

        Monitor::create(['url' => 'http://site3.com', 'uptime_check_enabled' => true, 'certificate_check_enabled' => false]);

        Monitor::create(['url' => 'http://site4.com', 'uptime_check_enabled' => true, 'certificate_check_enabled' => true]);

        $monitors = MonitorRepository::getForCertificateCheck();

        $this->assertEquals(['http://site2.com', 'http://site4.com'], $this->getMonitorUrls($monitors));
    }

    /** @test */
    public function it_can_get_all_monitors_with_certificate_problems()
    {
        Monitor::create([
            'url' => 'http://site1.com',
            'uptime_check_enabled' => false,
            'certificate_check_enabled' => false,
            'certificate_status' => CertificateStatus::INVALID,
        ]);

        Monitor::create([
            'url' => 'http://site2.com',
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => false,
            'certificate_status' => CertificateStatus::INVALID,
        ]);

        Monitor::create([
            'url' => 'http://site3.com',
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::INVALID,
        ]);

        Monitor::create([
            'url' => 'http://site4.com',
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::VALID,
        ]);

        Monitor::create([
            'url' => 'http://site5.com',
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
            'certificate_status' => CertificateStatus::NOT_YET_CHECKED,
        ]);

        $monitors = MonitorRepository::getWithFailingCertificateCheck();

        $this->assertEquals(['http://site3.com'], $this->getMonitorUrls($monitors));
    }

    protected function getMonitorUrls(Collection $monitors)
    {
        return $monitors
            ->pluck('url')
            ->map(function (Url $url) {
                return trim($url, '/');
            })
        ->toArray();
    }
}
