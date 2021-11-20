<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded as MonitorSucceededEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class UptimeCheckSucceeded extends BaseNotification
{
    public MonitorSucceededEvent $event;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage())
            ->subject($this->getMessageText())
            ->line($this->getMessageText())
            ->line($this->getLocationDescription());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->fallback($this->getMessageText())
                    ->footer($this->getLocationDescription())
                    ->timestamp(Carbon::now());
            });
    }

    public function isStillRelevant(): bool
    {
        return $this->getMonitor()->uptime_status != UptimeStatus::DOWN;
    }

    public function setEvent(MonitorSucceededEvent $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "{$this->getMonitor()->url} is up";
    }
}
