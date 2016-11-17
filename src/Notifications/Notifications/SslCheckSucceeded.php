<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\SslCheckSucceeded as ValidSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class SslCheckSucceeded extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\SslCheckSucceeded */
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
            ->subject("The ssl certificate check for {$this->event->monitor->url} succeeded.")
            ->line("The ssl certificate check for {$this->event->monitor->url} succeeded.");

        foreach($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name . ': ' . $value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("The ssl certificate check for {$this->event->monitor->url} succeeded.")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function setEvent(ValidSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
