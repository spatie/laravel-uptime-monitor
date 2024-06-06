<?php

namespace Spatie\UptimeMonitor\Test\Integration\Notifications;

use Carbon\Carbon;
use Notification;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed as UptimeCheckFailedEvent;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered as UptimeCheckRecoveredEvent;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded as UptimeCheckSucceededEvent;
use Spatie\UptimeMonitor\Helpers\Period;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Notifications\Notifiable;
use Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckSucceeded as InvalidCertificateFoundNotification;
use Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckFailed;
use Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckSucceeded;
use Spatie\UptimeMonitor\Test\TestCase;

class EventHandlerTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp(): void
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
            'uptime-monitor.notifications.notifications.'.UptimeCheckSucceeded::class,
            ['slack']
        );

        $monitor = Monitor::factory()->create($monitorAttributes);

        if (in_array($eventClass, [
            UptimeCheckFailedEvent::class,
            UptimeCheckRecoveredEvent::class,
        ])) {
            event(new $eventClass($monitor, new Period(Carbon::now(), Carbon::now())));
        } else {
            event(new $eventClass($monitor));
        }

        if ($shouldSendNotification) {
            Notification::assertSentTo(
                new Notifiable(),
                $notificationClass,
                function ($notification) use ($monitor) {
                    return $notification->event->monitor->id == $monitor->id;
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

    public static function eventClassDataProvider(): array
    {
        return [
            [UptimeCheckSucceededEvent::class, UptimeCheckSucceeded::class, ['uptime_status' => UptimeStatus::UP], true],
            [UptimeCheckSucceededEvent::class, UptimeCheckSucceeded::class, ['uptime_status' => UptimeStatus::DOWN], false],
            [UptimeCheckFailedEvent::class, UptimeCheckFailed::class, ['uptime_status' => UptimeStatus::DOWN], true],
            [UptimeCheckFailedEvent::class, UptimeCheckFailed::class, ['uptime_status' => UptimeStatus::UP], false],
            [UptimeCheckRecoveredEvent::class, UptimeCheckRecovered::class, ['uptime_status' => UptimeStatus::UP], true],
            [UptimeCheckRecoveredEvent::class, UptimeCheckRecovered::class, ['uptime_status' => UptimeStatus::DOWN], false],
        ];
    }

    public function it_send_a_notification_when_the_invalid_certificate_event_is_fired()
    {
        $monitor = Monitor::factory()->create();

        event(new CertificateCheckFailed($monitor, 'fail reason'));

        Notification::assertSentTo(
            new Notifiable(),
            InvalidCertificateFoundNotification::class,
            function ($notification) use ($monitor) {
                return $notification->event->monitor->id == $monitor->id;
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
            'uptime-monitor.notifications.notifications.'.UptimeCheckSucceeded::class,
            $configuredChannels
        );

        $monitor = Monitor::factory()->create();

        event(new UptimeCheckSucceededEvent($monitor));

        Notification::assertSentTo(
            new Notifiable(),
            UptimeCheckSucceeded::class,
            function ($notification, $usedChannels) use ($configuredChannels) {
                return $usedChannels == $configuredChannels;
            }
        );
    }

    public static function channelDataProvider(): array
    {
        return [
            [['mail']],
            [['mail', 'slack']],
        ];
    }
}
