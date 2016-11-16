<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\SoonExpiringSslCertificateFound as SoonExpiringSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class SoonExpiringSslCertificateFound extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\ValidSslCertificateFound */
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
            ->subject("The certificate for {$this->event->monitor->url} will expire soon.")
            ->line("The certificate for {$this->event->monitor->url} will expire in {$this->ssl_certificate_expiration_date->diffInDays()} days");

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("The certificate for {$this->event->monitor->url} will expire in {$this->event->monitor->ssl_certificate_expiration_date->diffInDays()} days")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function setEvent(SoonExpiringSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
