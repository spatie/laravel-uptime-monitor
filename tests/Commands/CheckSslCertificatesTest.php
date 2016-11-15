<?php

namespace Spatie\UptimeMonitor\Test\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckSslCertificatesCommandTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_check_ssl_certificates()
    {
        $site = factory(Site::class)->create(['check_ssl_certificate' => true]);

        Artisan::call('sites:check-ssl');

        $site = $site->fresh();

        $this->assertEquals(UptimeStatus::UP, $site->uptime_status);

        $this->seeInConsoleOutput("Checking ssl-certificate of {$site->url}");
    }


    public function it_can_check_the_ssl_certificate_of_a_specific_site()
    {
        $site1 = factory(Site::class)->create(['check_ssl_certificate' => true]);
        $site2 = factory(Site::class)->create([
            'url' => 'https://google.com',
            'check_ssl_certificate' => true
        ]);

        Artisan::call('sites:check-uptime', ['--url' => $site1->url]);

        $this->seeInConsoleOutput("Checking ssl-certificate of {$site1->url}");
        $this->dontSeeInConsoleOutput("Checking ssl-certificate of {$site2->url}");
    }

    public function it_can_check_the_ssl_certificate_of_multiple_specific_sites()
    {


        $site1 = factory(Site::class)->create(['check_ssl_certificate' => true]);
        $site2 = factory(Site::class)->create([
            'url' => 'https://google.com',
            'check_ssl_certificate' => true
        ]);
        $site3 = factory(Site::class)->create([
            'url' => 'https://bing.com',
            'check_ssl_certificate' => true
        ]);

        Artisan::call('sites:check-uptime', ['--url' => $site1->url . "," . $site2->url]);

        $this->seeInConsoleOutput("Checking ssl-certificate of {$site1->url}");
        $this->seeInConsoleOutput("Checking ssl-certificate of {$site2->url}");
        $this->dontSeeInConsoleOutput("Checking ssl-certificate of {$site3->url}");
    }
}
