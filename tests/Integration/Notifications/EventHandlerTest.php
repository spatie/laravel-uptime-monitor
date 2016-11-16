<?php

namespace Spatie\UptimeMonitor\Test\Integration\Notifications;

use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound;
use Spatie\UptimeMonitor\Events\MonitorRecovered as SiteRestoredEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Notifications\Notifiable;
use Spatie\UptimeMonitor\Notifications\Notifications\InvalidSslCertificateFound as InvalidSslCertificateFoundNotification;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteDown;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteRestored;
use Spatie\UptimeMonitor\Notifications\Notifications\SiteUp;
use Spatie\UptimeMonitor\Test\TestCase;
use Spatie\UptimeMonitor\Events\MonitorHealthy as SiteUpEvent;
use Spatie\UptimeMonitor\Events\MonitorFailed as SiteDownEvent;
use Notification;

class EventHandlerTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

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
    public function it_can_send_a_notifications_for_certain_events(
        $eventClass,
        $notificationClass,
        $monitorAttributes,
        $shouldSendNotification
    ) {
        $this->app['config']->set(
            'laravel-uptime-monitor.notifications.notifications.'.SiteUp::class,
            ['slack']
        );

        $monitor = factory(Monitor::class)->create($monitorAttributes);

        event(new $eventClass($monitor));

        if ($shouldSendNotification) {
            Notification::assertSentTo(
                new Notifiable(),
                $notificationClass,
                function ($notification) use ($monitor) {
                    return $notification->event->site->id == $monitor->id;
                }
            );
        }

        if (! $shouldSendNotification) {
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

    public function it_send_a_notification_when_the_invalid_ssl_certificate_event_is_fired()
    {
        $monitor = factory(Monitor::class)->create();

        event(new InvalidSslCertificateFound($monitor, 'fail reason'));

        Notification::assertSentTo(
            new Notifiable(),
            InvalidSslCertificateFoundNotification::class,
            function ($notification) use ($monitor) {
                return $notification->event->site->id == $monitor->id;
            }
        );
    }

    /**
     * @test
     *
     * @dataProvider channelDataProvider
     */
    public function it_send_notifications_to_the_channels_configured_in_the_config_file(array $configuredChannels)
    {
        $this->app['config']->set(
            'laravel-uptime-monitor.notifications.notifications.'.SiteUp::class,
            $configuredChannels
        );

        $monitor = factory(Monitor::class)->create();

        event(new SiteUpEvent($monitor));


        Notification::assertSentTo(
            new Notifiable(),
            SiteUp::class,
            function ($notification, $usedChannels) use ($configuredChannels) {
                return $usedChannels == $configuredChannels;
            }
        );
    }

    public function channelDataProvider(): array
    {
        return [
            [['mail']],
            [['mail', 'slack']],
        ];
    }
}
