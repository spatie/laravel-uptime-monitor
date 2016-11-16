<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\Test\TestCase;

class InvalidSslCertificateFoundTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->monitor = factory(Monitor::class)->create(['check_ssl_certificate' => true]);
    }

    /** @test */
    public function the_invalid_ssl_certificate_found_event_will_be_fired_when_an_invalid_ssl_certificate_is_found()
    {
        $this->monitor->checkSslCertificate();

        Event::assertFired(InvalidSslCertificateFound::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }
}
