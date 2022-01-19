<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\Integration\Helpers\ResponseCheckerFailureFake;
use Spatie\UptimeMonitor\Test\TestCase;

class CheckUptimeCommandTest extends TestCase
{
    /** @test */
    public function it_has_a_command_to_perform_uptime_checks()
    {
        $monitor = Monitor::factory()->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);

        Artisan::call('monitor:check-uptime');

        $monitor = $monitor->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor->uptime_status);
    }

    /** @test */
    public function it_can_perform_an_uptime_check_for_specific_monitor()
    {
        $monitor1 = Monitor::factory()->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $monitor2 = Monitor::factory()->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        Artisan::call('monitor:check-uptime', ['--url' => $monitor1->url]);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor1->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $monitor2->uptime_status);
    }

    /** @test */
    public function it_can_perform_an_uptime_checks_for_a_set_of_specific_monitors()
    {
        $this->skipIfNotConnectedToTheInternet();

        $monitor1 = Monitor::factory()->create(['uptime_status' => UptimeStatus::NOT_YET_CHECKED]);
        $monitor2 = Monitor::factory()->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://google.com',
        ]);

        $monitor3 = Monitor::factory()->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'url' => 'https://bing.com',
        ]);

        Artisan::call('monitor:check-uptime', ['--url' => $monitor1->url.','.$monitor2->url]);

        $monitor1 = $monitor1->fresh();
        $monitor2 = $monitor2->fresh();
        $monitor3 = $monitor3->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor1->uptime_status);
        $this->assertEquals(UptimeStatus::UP, $monitor2->uptime_status);
        $this->assertEquals(UptimeStatus::NOT_YET_CHECKED, $monitor3->uptime_status);
    }

    /** @test */
    public function it_can_post_a_payload()
    {
        $monitor = Monitor::factory()->create([
            'url' => sprintf('http://localhost:%s/testPost', env('TEST_SERVER_PORT')),
            'uptime_check_method' => 'post',
            'uptime_check_payload' => json_encode(['foo' => 'bar']),
            'uptime_check_additional_headers' => ['Content-Type' => 'application/json'],
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
        ]);

        Artisan::call('monitor:check-uptime');

        $monitor = $monitor->fresh();

        $this->assertEquals(UptimeStatus::UP, $monitor->uptime_status);
    }

    /** @test */
    public function it_can_use_a_custom_response_checker()
    {
        $monitor = Monitor::factory()->create([
            'uptime_status' => UptimeStatus::NOT_YET_CHECKED,
            'uptime_check_response_checker' => ResponseCheckerFailureFake::class,
        ]);

        Artisan::call('monitor:check-uptime');

        $monitor = $monitor->fresh();

        $this->assertEquals(UptimeStatus::DOWN, $monitor->uptime_status);
        $this->assertEquals(ResponseCheckerFailureFake::FAILURE_REASON, $monitor->uptime_check_failure_reason);
    }
}
