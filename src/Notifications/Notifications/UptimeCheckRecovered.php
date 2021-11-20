<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered as MonitorRecoveredEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class UptimeCheckRecovered extends BaseNotification
{
    public MonitorRecoveredEvent $event;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage())
            ->success()
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
            ->success()
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->fallback($this->getMessageText())
                    ->footer($this->getLocationDescription())
                    ->timestamp(Carbon::now());
            });
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $extraProperties = [
            "Downtime: {$this->event->downtimePeriod->duration()}" => $this->event->downtimePeriod->toText(),
        ];

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->getMonitor()->uptime_status == UptimeStatus::UP;
    }

    public function setEvent(MonitorRecoveredEvent $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "{$this->getMonitor()->url} has recovered after {$this->event->downtimePeriod->duration()}";
    }
}
