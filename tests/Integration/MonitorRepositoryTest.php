<?php

namespace Spatie\UptimeMonitor\Test\Integration;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\SslCertificateStwatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\Url\Url;

class MonitorRepositoryTest extends TestCase
{
    /** @test */
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_get_all_enabled_monitors()
    {
        Monitor::create(['url' => 'http://enabled1.com', 'enabled' => true]);

        Monitor::create(['url' => 'http://disabled1.com', 'enabled' => false]);

        Monitor::create(['url' => 'http://enabled2.com', 'enabled' => true]);

        Monitor::create(['url' => 'http://disabled2.com', 'enabled' => false]);

        $enabledMonitors = MonitorRepository::getEnabled();

        $this->assertEquals(['http://enabled1.com', 'http://enabled2.com'], $this->getMonitorUrls($enabledMonitors));
    }

    /** @test */
    public function it_can_get_all_disabled_monitors()
    {
        Monitor::create(['url' => 'http://enabled1.com', 'enabled' => true]);

        Monitor::create(['url' => 'http://disabled1.com', 'enabled' => false]);

        Monitor::create(['url' => 'http://enabled2.com', 'enabled' => true]);

        Monitor::create(['url' => 'http://disabled2.com', 'enabled' => false]);

        $disabledMonitors = MonitorRepository::getDisabled();

        $this->assertEquals(['http://disabled1.com', 'http://disabled2.com'], $this->getMonitorUrls($disabledMonitors));
    }

    /** @test */
    public function it_can_get_all_monitors_that_are_failing()
    {
        Monitor::create(['url' => 'http://down1.com', 'uptime_status' => UptimeStatus::DOWN]);

        Monitor::create(['url' => 'http://up.com', 'uptime_status' => UptimeStatus::UP]);

        Monitor::create(['url' => 'http://down2.com', 'uptime_status' => UptimeStatus::DOWN]);

        $failingMonitors = MonitorRepository::getFailing();

        $this->assertEquals(['http://down1.com', 'http://down2.com'], $this->getMonitorUrls($failingMonitors));
    }

    /** @test */
    public function it_can_get_all_monitors_that_need_an_ssl_certificate_check()
    {
        Monitor::create(['url' => 'http://site1.com', 'enabled' => false, 'check_ssl_certificate' => false]);

        Monitor::create(['url' => 'http://site2.com', 'enabled' => false, 'check_ssl_certificate' => true]);

        Monitor::create(['url' => 'http://site3.com', 'enabled' => true, 'check_ssl_certificate' => false]);

        Monitor::create(['url' => 'http://site4.com', 'enabled' => true, 'check_ssl_certificate' => true]);

        $monitors = MonitorRepository::getForSslCheck();

        $this->assertEquals(['http://site4.com'], $this->getMonitorUrls($monitors));
    }

    /** @test */
    public function it_can_get_all_monitors_with_ssl_problems()
    {
        Monitor::create([
            'url' => 'http://site1.com',
            'enabled' => false,
            'check_ssl_certificate' => false,
            'ssl_certificate_status' => SslCertificateStatus::INVALID
        ]);

        Monitor::create([
            'url' => 'http://site2.com',
            'enabled' => true,
            'check_ssl_certificate' => false,
            'ssl_certificate_status' => SslCertificateStatus::INVALID
        ]);

        Monitor::create([
            'url' => 'http://site3.com',
            'enabled' => true,
            'check_ssl_certificate' => true,
            'ssl_certificate_status' => SslCertificateStatus::INVALID
        ]);

        Monitor::create([
            'url' => 'http://site4.com',
            'enabled' => true,
            'check_ssl_certificate' => true,
            'ssl_certificate_status' => SslCertificateStatus::VALID
        ]);

        Monitor::create([
            'url' => 'http://site5.com',
            'enabled' => true,
            'check_ssl_certificate' => true,
            'ssl_certificate_status' => SslCertificateStatus::NOT_YET_CHECKED
        ]);

        $monitors = MonitorRepository::getWithSslProblems();

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
