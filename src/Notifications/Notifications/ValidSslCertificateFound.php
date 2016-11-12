<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\ValidSslCertificateFound as ValidSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class ValidSslCertificateFound extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\ValidSslCertificateFound */
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
            ->subject("Found a valid certificate for {$this->event->site->url}.")
            ->line('Found a valid certificate');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("Found a valid ssl certificate for {$this->event->site->url}")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getSiteProperties());
            });
    }

    public function setEvent(ValidSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
