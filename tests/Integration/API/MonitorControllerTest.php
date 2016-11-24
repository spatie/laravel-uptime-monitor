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
        $monitor = factory(Monitor::class)->create();
        $this->json('DELETE', route('monitor.destroy', ['monitor' => $monitor->id]))
            ->seeJson([
                'deleted' => true,
            ])->seeNotInDatabase('monitors', [
                'url' => $monitor->url
            ]);
    }

    /** @test */
    public function test_monitor_update_api_call()
    {
        $monitor = factory(Monitor::class)->create();
        $this->json('PUT', route('monitor.update', ['monitor' => $monitor->id], ['url' => 'http://updated.com']))
            ->seeJson([
                'updated' => true,
            ])->seeInDatabase('monitors', [
                'url' => 'http://updated.com'
            ]);
    }

    /** @test */
    public function test_monitor_get_from_id_api_call()
    {

    }

    /** @test */
    public function test_monitor_get_all_api_call()
    {

    }
}
