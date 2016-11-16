<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\MonitorSucceeded as MonitorSucceededEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class MonitorSucceeded extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\MonitorSucceeded */
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
            ->subject("Site {$this->event->monitor->url} is up.")
            ->line('Site is up');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("Site {$this->event->monitor->url} is up")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function isStillRelevant(): bool
    {
        return $this->event->monitor->uptime_status != UptimeStatus::DOWN;
    }

    public function setEvent(MonitorSucceededEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
