<?php

namespace Spatie\UptimeMonitor\Actions;

use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Spatie\UptimeMonitor\Models\Monitor;

class SendUptimeCheckRequestAction
{
    protected ?ClientInterface $client = null;

    public function execute(Monitor $monitor): PromiseInterface
    {
        return $this->client()->requestAsync(
            $monitor->uptime_check_method,
            $monitor->url,
            $this->requestOptions($monitor)
        );
    }

    protected function client(): ClientInterface
    {
        return $this->client ??= $this->makeClient();
    }

    protected function makeClient(): ClientInterface
    {
        return GuzzleFactory::make(
            config('uptime-monitor.uptime_check.guzzle_options', []),
            config('uptime-monitor.uptime_check.retry_connection_after_milliseconds', 100)
        );
    }

    protected function requestOptions(Monitor $monitor): array
    {
        return array_filter([
            'connect_timeout' => config('uptime-monitor.uptime_check.timeout_per_site'),
            'headers' => $this->headers($monitor),
            'body' => $monitor->uptime_check_payload,
        ]);
    }

    protected function headers(Monitor $monitor): array
    {
        return collect()
            ->merge(['User-Agent' => config('uptime-monitor.uptime_check.user_agent')])
            ->merge(config('uptime-monitor.uptime_check.additional_headers') ?? [])
            ->merge($monitor->uptime_check_additional_headers)
            ->toArray();
    }
}
