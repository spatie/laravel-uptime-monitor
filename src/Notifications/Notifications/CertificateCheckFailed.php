<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Spatie\UptimeMonitor\Notifications\BaseNotification;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed as InValidCertificateFoundEvent;

class CertificateCheckFailed extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\CertificateCheckFailed */
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
            ->error()
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->content($this->getMonitor()->certificate_check_failure_reason)
                    ->fallback($this->getMessageText())
                    ->footer($this->getMonitor()->certificate_issuer)
                    ->timestamp(Carbon::now());
            });
    }

    public function getMonitorProperties($properties = []): array
    {
        $extraProperties = ['Failure reason' => $this->event->monitor->certificate_check_failure_reason];

        return parent::getMonitorProperties($extraProperties);
    }

    public function setEvent(InValidCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "SSL Certificate for {$this->getMonitor()->url} is invalid";
    }
}
