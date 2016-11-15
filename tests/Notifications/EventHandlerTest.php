<?php

namespace Spatie\UptimeMonitor\Test\Notifications;

use Spatie\UptimeMonitor\Events\SiteRestored as SiteRestoredEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Notifications\Notifiable;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteDown;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteRestored;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteUp;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\UptimeMonitor\Events\SiteUp as SiteUpEvent;
use Spatie\UptimeMonitor\Events\SiteDown as SiteDownEvent;
use Notification;

class EventHandlerTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        Notification::fake();
    }

    /**
     * @test
     *
     * @dataProvider eventClassDataProvider
     */
    public function it_can_send_a_notification_for_an_up_event(
        $eventClass,
        $notificationClass,
        $siteAttributes,
        $shouldSendNotification
    )
    {
        $this->app['config']->set(
            'laravel-uptime-monitor.notifications.notifications.' . SiteUp::class,
            ['slack']
        );

        $site = factory(Site::class)->create($siteAttributes);

        event(new $eventClass($site));

        if ($shouldSendNotification) {
            Notification::assertSentTo(
                new Notifiable(),
                $notificationClass,
                function ($notification) use ($site) {
                    return $notification->event->site->id == $site->id;
                }
            );
        }

        if (!$shouldSendNotification) {
            Notification::assertNotSentTo(
                new Notifiable(),
                $notificationClass
            );
        }

    }

    public function eventClassDataProvider(): array
    {
        return [
            [SiteUpEvent::class, SiteUp::class, ['uptime_status' => UptimeStatus::UP], true],
            [SiteUpEvent::class, SiteUp::class, ['uptime_status' => UptimeStatus::DOWN], false],
            [SiteDownEvent::class, SiteDown::class, ['uptime_status' => UptimeStatus::DOWN], true],
            [SiteDownEvent::class, SiteDown::class, ['uptime_status' => UptimeStatus::UP], false],
            [SiteRestoredEvent::class, SiteRestored::class, ['uptime_status' => UptimeStatus::UP], true],
            [SiteRestoredEvent::class, SiteRestored::class, ['uptime_status' => UptimeStatus::DOWN], false],
        ];
    }

}