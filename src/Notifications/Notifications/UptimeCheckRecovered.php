<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered as MonitorRecoveredEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Notifications\BaseNotification;

class UptimeCheckRecovered extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\UptimeCheckRecovered */
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
            ->subject("The uptime check for {$this->event->monitor->url} has recovered.")
            ->line("The uptime check for {$this->event->monitor->url} has recovered.");

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content("The uptime check for {$this->event->monitor->url} has recovered.")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->getMonitorProperties());
            });
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $extraProperties = [
            'Back online since' => $this->event->monitor->formattedLastUpdatedStatusChangeDate,
            'Offline period length' => $this->event,
        ];

        if ($failureStartDate = $this->event->uptimeCheckStartedFailingOnDate) {
            $extraProperties['Offline period length'] = $failureStartDate->diffForHumans();
        }

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->event->monitor->uptime_status == UptimeStatus::UP;
    }

    public function setEvent(MonitorRecoveredEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
