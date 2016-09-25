<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\SiteUp as SiteUpEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class SiteUp extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\SiteDown */
    protected $event;

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
            ->subject("Site {$this->event->uptimeMonitor->url} is up.")
            ->line('Site is up');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("Site {$this->event->uptimeMonitor->url} is up")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getUptimeMonitorProperties());
            });
    }

    public function isStillRelevant(): bool
    {
        return $this->event->uptimeMonitor->uptime_status == UptimeStatus::UP;
    }

    public function getUptimeMonitorProperties($extraProperties = []): array
    {
        $extraProperties = [
            'online since' => $this->event->uptimeMonitor->last_uptime_status_change_on->diffForHumans(),
        ];

        return parent::getUptimeMonitorProperties($extraProperties);
    }

    public function setEvent(SiteUpEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
