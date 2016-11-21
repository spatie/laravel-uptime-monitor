<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\CertificateExpiresSoon as SoonExpiringSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class CertificateExpiresSoon extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\CertificateExpiresSoon */
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
            ->content($this->getMessageText())
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function setEvent(SoonExpiringSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    protected function getMessageText(): string
    {
        return "{$this->event->monitor->url} has a certificate that will expire soon{$this->getLocationDescription()}.";
    }
}
