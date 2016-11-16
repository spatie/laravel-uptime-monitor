<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class EnableCommandTest extends TestCase
{
    /** @test */
    public function it_can_enable_a_disabled_monitor()
    {
        $monitor = factory(Monitor::class)->create([
            'enabled' => false,
            'url' => 'http://mysite.com',
        ]);

        $this->assertFalse($monitor->fresh()->enabled);

        $this->artisan('monitor:enable', ['url' => 'http://mysite.com']);

        $this->assertTrue($monitor->fresh()->enabled);
    }

    /** @test */
    public function it_displays_a_message_if_the_monitor_is_not_found()
    {
        $this->artisan('monitor:enable', ['url' => 'http://mysite.com']);

        $this->seeInConsoleOutput('There is no monitor configured for url');
    }

    /** @test */
    public function it_displays_a_message_if_the_monitor_was_already_enabled()
    {
        factory(Monitor::class)->create([
            'enabled' => true,
            'url' => 'http://mysite.com',
        ]);

        $this->artisan('monitor:enable', ['url' => 'http://mysite.com']);

        $this->seeInConsoleOutput('already enabled');
    }

    /** @test */
    public function it_can_enable_multiple_urls_at_once()
    {
        $monitor1 = factory(Monitor::class)->create([
            'enabled' => false,
            'url' => 'http://mysite.com',
        ]);

        $monitor2 = factory(Monitor::class)->create([
            'enabled' => false,
            'url' => 'http://mysite2.com',
        ]);

        $this->artisan('monitor:enable', ['url' => 'http://mysite.com, http://mysite2.com']);

        $this->assertTrue($monitor1->fresh()->enabled);
        $this->assertTrue($monitor2->fresh()->enabled);

    }
}
