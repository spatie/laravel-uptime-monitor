<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\InvalidSslCertificateFound as InvalidSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class InvalidSslCertificateFound extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\InvalidSslCertificateFound */
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
            ->subject("Found an invalid certificate for {$this->event->monitor->url}.")
            ->line('Found an invalid certificate');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("Found an invalid ssl certificate for {$this->event->monitor->url}")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function getMonitorProperties($properties = []): array
    {
        $extraProperties = ['failure reason' => $this->event->monitor->ssl_certificate_failure_reason];

        return parent::getMonitorProperties($extraProperties);
    }

    public function setEvent(InvalidSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
