<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class CertificateCheckFailedTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->monitor = Monitor::factory()->create(['certificate_check_enabled' => true]);
    }

    /** @test */
    public function the_invalid_certificate_found_event_will_be_fired_when_an_invalid_certificate_is_found()
    {
        $this->monitor->checkCertificate();

        Event::assertDispatched(CertificateCheckFailed::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }
}
