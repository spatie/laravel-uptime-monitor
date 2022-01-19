<?php

namespace Spatie\UptimeMonitor\Test;

use Spatie\UptimeMonitor\Exceptions\InvalidConfiguration;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;

class ConfigurationTest extends TestCase
{
    /** @test */
    public function a_custom_monitor_model_can_be_specified()
    {
        Monitor::factory()->create();

        $customModel = new class () extends Monitor {
            public $table = 'monitors';
        };

        $this->app['config']->set('uptime-monitor.monitor_model', get_class($customModel));

        $this->assertInstanceOf(get_class($customModel), MonitorRepository::getEnabled()->first());
    }

    /** @test */
    public function when_an_invalid_monitor_model_is_specified_an_exception_will_be_thrown()
    {
        $customModel = new class () {
        };

        $this->app['config']->set('uptime-monitor.monitor_model', get_class($customModel));

        $this->expectException(InvalidConfiguration::class);

        MonitorRepository::getEnabled();
    }
}
