<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound;
use Spatie\UptimeMonitor\Events\ValidSslCertificateFound;
use Spatie\UptimeMonitor\Models\Monitor;
use Event;
use Spatie\UptimeMonitor\Test\TestCase;

class ValidSslCertificateFoundTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow(null);

        Event::fake();

        $this->monitor = factory(Monitor::class)->create([
            'check_ssl_certificate' => true,
            'url' => 'https://google.com',
        ]);
    }

    /** @test */
    public function the_valid_ssl_certificate_found_event_will_be_fired_when_a_valid_ssl_certificate_is_found()
    {
        $this->skipIfNotConnectedToTheInternet();

        $this->monitor->checkSslCertificate();

        Event::assertFired(ValidSslCertificateFound::class, function ($event) {
            return $event->site->id === $this->monitor->id;
        });
    }
}
