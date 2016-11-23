<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded as ValidCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class CertificateCheckSucceeded extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\CertificateCheckSucceeded */
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
            ->subject($this->getMessageText())
            ->line($this->getMessageText());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("The certificate check for {$this->event->monitor->url} succeeded.")
            ->attachment($this->get)
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function setEvent(ValidCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "{$this->event->monitor->url} has a valid certificate{$this->getLocationDescription()}.";
    }
}
