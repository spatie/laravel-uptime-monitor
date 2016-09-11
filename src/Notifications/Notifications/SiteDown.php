<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\UptimeMonitor\Events\SiteDown as SiteDownEvent;

class SiteDown extends Notification
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
            ->subject("Site {$this->event->uptimeMonitor->url} is down")
            ->line("Site is down");

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("Site {$this->event->uptimeMonitor->url} is down");
    }

    public function setEvent(SiteDownEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
