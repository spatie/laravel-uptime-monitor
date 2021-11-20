<?php

namespace Spatie\UptimeMonitor\Test\Integration\Helpers;

use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers\UptimeResponseChecker;
use Spatie\UptimeMonitor\Models\Monitor;

class ResponseCheckerFailureFake implements UptimeResponseChecker
{
    public const FAILURE_REASON = 'FAKE_CHECK';

    public function isValidResponse(ResponseInterface $response, Monitor $monitor): bool
    {
        return false;
    }

    public function getFailureReason(ResponseInterface $response, Monitor $monitor): string
    {
        return self::FAILURE_REASON;
    }
}
