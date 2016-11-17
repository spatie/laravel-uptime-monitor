<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed as InValidCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class CertificateCheckFailed extends BaseNotification
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
            ->error()
            ->subject("The certificate check for {$this->event->monitor->url} failed.")
            ->line("The certificate check for {$this->event->monitor->url} failed.");

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content("The certificate check for {$this->event->monitor->url} failed.")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function getMonitorProperties($properties = []): array
    {
        $extraProperties = ['Failure reason' => $this->event->monitor->certificate_failure_reason];

        return parent::getMonitorProperties($extraProperties);
    }

    public function setEvent(InValidCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
