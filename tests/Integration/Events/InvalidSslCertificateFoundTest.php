<?php

namespace Spatie\UptimeMonitor\Test\Integration\Events;

use Artisan;
use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound;
use Spatie\UptimeMonitor\Models\Site;
use Event;
use Spatie\UptimeMonitor\Test\TestCase;

class InvalidSslCertificateFoundTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->site = factory(Site::class)->create(['check_ssl_certificate' => true]);
    }

    /** @test */
    public function the_invalid_ssl_certificate_found_event_will_be_fired_when_an_invalid_ssl_certificate_is_found()
    {
        $this->site->checkSslCertificate();

        Event::assertFired(InvalidSslCertificateFound::class, function ($event) {
            return $event->site->id === $this->site->id;
        });

    }
}