<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\SiteDown as SiteDownEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class SiteDown extends BaseNotification
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
            ->error()
            ->subject("Site {$this->event->site->url} is down.")
            ->line('Site is down');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("Site {$this->event->site->url} is down")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getUptimeMonitorProperties());
            });
    }

    public function getUptimeMonitorProperties($extraProperties = []): array
    {
        $extraProperties = [
            'offline since' => $this->event->site->formattedLastUpdatedStatusChangeDate,
        ];

        return parent::getUptimeMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->event->site->uptime_status == UptimeStatus::DOWN;
    }

    public function setEvent(SiteDownEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
