<?php

namespace Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers;

use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Models\Monitor;

class LookForStringChecker implements UptimeResponseChecker
{
    public function isValidResponse(ResponseInterface $response, Monitor $monitor): bool
    {


        if (empty($monitor->look_for_string)) {
            return true;
        }

        return str_contains((string)$response->getBody(), $monitor->look_for_string);
    }

    public function getFailureReason(ResponseInterface $response, Monitor $monitor): string
    {
        return "String `{$monitor->look_for_string}` was not found on the response.";
    }
}