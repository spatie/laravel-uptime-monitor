<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Event;
use Carbon\Carbon;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded;

class CertificateCheckSucceededTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow(null);

        Event::fake();

        $this->monitor = factory(Monitor::class)->create([
            'certificate_check_enabled' => true,
            'url' => 'https://google.com',
        ]);
    }

    /** @test */
    public function the_valid_certificate_found_event_will_be_fired_when_a_valid_certificate_is_found()
    {
        $this->skipIfNotConnectedToTheInternet();

        $this->monitor->checkCertificate();

        Event::assertFired(CertificateCheckSucceeded::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }
}
