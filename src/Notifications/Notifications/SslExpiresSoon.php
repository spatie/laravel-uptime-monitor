<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\SslExpiresSoon as SoonExpiringSslCertificateFoundEvent;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class SslExpiresSoon extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\SslExpiresSoon */
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

        foreach($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name . ': ' . $value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content($this->getMessageText())
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    protected function getMessageText(): string
    {
        return "The certificate for {$this->event->monitor->url} will expire in {$this->ssl_certificate_expiration_date->diffInDays()} days.";
    }

    public function setEvent(SoonExpiringSslCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
