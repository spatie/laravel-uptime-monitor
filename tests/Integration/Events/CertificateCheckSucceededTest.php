<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class CertificateCheckSucceededTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(null);

        Event::fake();

        $this->monitor = Monitor::factory()->create([
            'certificate_check_enabled' => true,
            'url' => 'https://google.com',
        ]);
    }

    /** @test */
    public function the_valid_certificate_found_event_will_be_fired_when_a_valid_certificate_is_found()
    {
        $this->skipIfNotConnectedToTheInternet();

        $this->monitor->checkCertificate();

        Event::assertDispatched(CertificateCheckSucceeded::class, function ($event) {
            return $event->monitor->id === $this->monitor->id;
        });
    }
}
