<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\MonitorFailed as MonitorFailedEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class MonitorFailed extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\MonitorFailed */
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
            ->error()
            ->subject("The uptime check for monitor {$this->event->monitor->url} failed.")
            ->line("The uptime check for monitor {$this->event->monitor->url} failed.");

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("The uptime check for monitor {$this->event->monitor->url} failed.")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $extraProperties = [
            'failing since' => $this->event->monitor->formattedLastUpdatedStatusChangeDate,
        ];

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->event->monitor->uptime_status == UptimeStatus::DOWN;
    }

    public function setEvent(MonitorFailedEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
