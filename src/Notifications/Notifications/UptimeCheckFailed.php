<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Illuminate\Notifications\Messages\SlackAttachment;
use Spatie\UptimeMonitor\Notifications\BaseNotification;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed as MonitorFailedEvent;

class UptimeCheckFailed extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\UptimeCheckFailed */
    public $event;

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->error()
            ->subject($this->getMessageText())
            ->line($this->getMessageText());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name . ': ' . $value);
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
                    ->content($this->getMonitor()->uptime_check_failure_reason)
                    ->footer($this->getLocationDescription())
                    ->timestamp(Carbon::now());
            });
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $since = "Since {$this->event->downtimePeriod->startDateTime->format('H:i')}";
        $date = $this->event->monitor->formattedLastUpdatedStatusChangeDate();

        $extraProperties = [
            $since => $date,
            'Failure reason' => $this->event->monitor->uptime_check_failure_reason,
        ];

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->event->monitor->uptime_status == UptimeStatus::DOWN;
    }

    public function setEvent(MonitorFailedEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    protected function getMessageText(): string
    {
        return "{$this->event->monitor->url} seems down";
    }
}
