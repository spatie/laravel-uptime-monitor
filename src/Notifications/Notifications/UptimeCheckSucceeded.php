<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Illuminate\Notifications\Messages\SlackAttachment;
use Spatie\UptimeMonitor\Notifications\BaseNotification;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded as MonitorSucceededEvent;

class UptimeCheckSucceeded extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\UptimeCheckSucceeded */
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
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->footer($this->getLocationDescription())
                    ->timestamp(Carbon::now());
            });
    }

    public function isStillRelevant(): bool
    {
        return $this->event->monitor->uptime_status != UptimeStatus::DOWN;
    }

    public function setEvent(MonitorSucceededEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "{$this->event->monitor->url} is up";
    }
}
