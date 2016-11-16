<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckSslCertificatesTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_check_ssl_certificates()
    {
        $monitor = factory(Monitor::class)->create(['check_ssl_certificate' => true]);

        Artisan::call('monitor:check-ssl');

        $monitor = $monitor->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor->uptime_status);

        $this->seeInConsoleOutput("Checking ssl-certificate of {$monitor->url}");
    }

    public function it_can_check_the_ssl_certificate_for_a_specific_monitor()
    {
        $monitor1 = factory(Monitor::class)->create(['check_ssl_certificate' => true]);
        $monitor2 = factory(Monitor::class)->create([
            'url' => 'https://google.com',
            'check_ssl_certificate' => true,
        ]);

        Artisan::call('monitor:check-uptime', ['--url' => $monitor1->url]);

        $this->seeInConsoleOutput("Checking ssl-certificate of {$monitor1->url}");
        $this->dontSeeInConsoleOutput("Checking ssl-certificate of {$monitor2->url}");
    }

    public function it_can_check_the_ssl_certificates_for_a_specific_set_of_monitors()
    {
        $monitor1 = factory(Monitor::class)->create(['check_ssl_certificate' => true]);
        $monitor2 = factory(Monitor::class)->create([
            'url' => 'https://google.com',
            'check_ssl_certificate' => true,
        ]);
        $monitor3 = factory(Monitor::class)->create([
            'url' => 'https://bing.com',
            'check_ssl_certificate' => true,
        ]);

        Artisan::call('monitor:check-uptime', ['--url' => $monitor1->url.','.$monitor2->url]);

        $this->seeInConsoleOutput("Checking ssl-certificate of {$monitor1->url}");
        $this->seeInConsoleOutput("Checking ssl-certificate of {$monitor2->url}");
        $this->dontSeeInConsoleOutput("Checking ssl-certificate of {$monitor3->url}");
    }
}
