<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckCertificatesTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_check_certificates()
    {
        $monitor = Monitor::factory()->create(['certificate_check_enabled' => true]);

        Artisan::call('monitor:check-certificate');

        $monitor = $monitor->fresh();

        $this->assertEquals(CertificateStatus::INVALID, $monitor->certificate_status);

        $this->seeInConsoleOutput("Checking certificate of {$monitor->url}");
    }

    /** @test */
    public function it_can_check_the_certificate_for_a_specific_monitor()
    {
        $monitor1 = Monitor::factory()->create(['certificate_check_enabled' => true]);
        $monitor2 = Monitor::factory()->create([
            'url' => 'https://google.com',
            'certificate_check_enabled' => true,
        ]);

        Artisan::call('monitor:check-certificate', ['--url' => $monitor1->url]);

        $output = Artisan::output();

        $this->assertStringContainsString("Checking certificate of {$monitor1->url}", $output);
        $this->assertStringNotContainsString("Checking certificate of {$monitor2->url}", $output);
    }

    /** @test */
    public function it_can_check_the_certificates_for_a_specific_set_of_monitors()
    {
        $monitor1 = Monitor::factory()->create(['certificate_check_enabled' => false]);
        $monitor2 = Monitor::factory()->create([
            'url' => 'https://google.com',
            'certificate_check_enabled' => true,
        ]);
        $monitor3 = Monitor::factory()->create([
            'url' => 'https://bing.com',
            'certificate_check_enabled' => true,
        ]);

        Artisan::call('monitor:check-certificate', ['--url' => $monitor2->url.','.$monitor3->url]);

        $output = Artisan::output();

        $this->assertStringNotContainsString("Checking certificate of {$monitor1->url}", $output);
        $this->assertStringContainsString("Checking certificate of {$monitor2->url}", $output);
        $this->assertStringContainsString("Checking certificate of {$monitor3->url}", $output);
    }
}
