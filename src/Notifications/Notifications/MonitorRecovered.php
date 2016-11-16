<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\MonitorRecovered as MonitorRecoveredEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class MonitorRecovered extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\MonitorRecovered */
    public $event;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->success()
            ->subject("Site {$this->event->monitor->url} has been restored.")
            ->line('Site has been restored');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("Site {$this->event->monitor->url} has been restored")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $extraProperties = [
            'online since' => $this->event->monitor->formattedLastUpdatedStatusChangeDate,
        ];

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->event->monitor->uptime_status == UptimeStatus::UP;
    }

    public function setEvent(MonitorRecoveredEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
