<?php

namespace Spatie\UptimeMonitor\Test\Integration\API;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class MonitorControllerTest extends TestCase
{
    /** @test */
    public function test_monitor_create_api_call()
    {
        $this->json('POST', route('monitor.store'), ['url' => 'http://test.com'])
            ->seeJson([
                'created' => true,
            ])->seeInDatabase('monitors', [
                'url' => 'http://test.com'
            ]);
    }

    /** @test */
    public function test_monitor_destroy_api_call()
    {
        $monitor = factory(Monitor::class)->create(['url' => 'http://destroy.com']);
        $this->assertEquals(1, Monitor::where('url', $monitor->url)->count());
        $this->json('DELETE', route('monitor.destroy', ['monitor' => $monitor->id]))
            ->seeJson([
                'deleted' => true,
            ]);
        $this->assertEquals(0, Monitor::where('url', $monitor->url)->count());
    }

    /** @test */
    public function test_monitor_update_api_call()
    {
        $monitor = factory(Monitor::class)->create(['url' => 'http://notupdated.com']);
        $this->json('PUT', route('monitor.update', ['monitor' => $monitor->id]), ['url' => 'http://updated.com'])
            ->seeJson([
                'updated' => true,
            ])->seeInDatabase('monitors', [
                'url' => 'http://updated.com'
            ]);
    }

    /** @test */
    public function test_monitor_get_from_id_api_call()
    {
        $monitor = factory(Monitor::class)->create(['url' => 'http://getFromID.com']);
        $this->json('GET', route('monitor.show', ['monitor' => $monitor->id]))
            // Little Hack, Return Model as String to get the same json that should come from the Request
            // than json_decode it to get the json as array
            ->seeJson(json_decode((string)$monitor, true));
    }

    /** @test */
    public function test_monitor_get_all_api_call()
    {
        factory(Monitor::class)->create(['url' => 'https://justOneWithSSL.com']);
        $this->json('GET', route('monitor.index'))
            ->seeJsonStructure([
                '*' => [
                    'id',
                    'url',
                    'uptime_check_enabled',
                    'look_for_string',
                    'uptime_check_interval_in_minutes',
                    'uptime_status',
                    'uptime_check_failure_reason',
                    'uptime_check_times_failed_in_a_row',
                    'uptime_status_last_change_date',
                    'uptime_last_check_date',
                    'uptime_check_failed_event_fired_on_date',
                    'uptime_check_method',
                    'certificate_check_enabled',
                    'certificate_status',
                    'certificate_expiration_date',
                    'certificate_issuer',
                    'certificate_check_failure_reason',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
