<?php

namespace Spatie\UptimeMonitor\Test\Notifications;

use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Notifications\Notifiable;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteUp;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\UptimeMonitor\Events\SiteUp as SiteUpEvent;
use Notification;

class EventHandlerTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site  */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        Notification::fake();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function it_can_send_a_notification_for_an_up_event()
    {
        $this->app['config']->set(
            'laravel-uptime-monitor.notifications.notifications.' . SiteUp::class,
            ['slack']
        );

        event(new SiteUpEvent($this->site));

        Notification::assertSentTo(
            new Notifiable(),
            SiteUp::class,
            function ($notification){
                return $notification->event->site->id == $this->site->id;
            }
        );
    }

}