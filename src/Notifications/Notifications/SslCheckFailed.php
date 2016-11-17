<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\SslCheckFailed as InvalidSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class SslCheckFailed extends BaseNotification
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
            ->error()
            ->subject("The ssl certificate check for {$this->event->monitor->url} failed.")
            ->line("The ssl certificate check for {$this->event->monitor->url} failed.");

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("The ssl certificate check for {$this->event->monitor->url} failed.")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function getMonitorProperties($properties = []): array
    {
        $extraProperties = ['Failure reason' => $this->event->monitor->ssl_certificate_failure_reason];

        return parent::getMonitorProperties($extraProperties);
    }

    public function setEvent(InvalidSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
