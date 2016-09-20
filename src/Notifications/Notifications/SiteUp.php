<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\UptimeMonitor\Events\SiteDown as SiteDownEvent;
use Spatie\UptimeMonitor\Models\UptimeMonitor;

class SiteUp extends Notification
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
            ->line("Site is up");

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("Site {$this->event->uptimeMonitor->url} is up");
    }

    public function isStillRelevant(): bool
    {
        return $this->event->uptimeMonitor->status == UptimeMonitor::STATUS_UP;
    }

    public function setEvent(SiteDownEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
