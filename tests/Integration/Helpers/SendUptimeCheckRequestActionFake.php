<?php

namespace Spatie\UptimeMonitor\Test\Integration\Helpers;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Spatie\UptimeMonitor\Actions\SendUptimeCheckRequestAction;
use Spatie\UptimeMonitor\Models\Monitor;

class SendUptimeCheckRequestActionFake extends SendUptimeCheckRequestAction
{
    public const RESPONSE_BODY = 'FAKE_RESPONSE';

    public function execute(Monitor $monitor): PromiseInterface
    {
        return Create::promiseFor(new Response(200, [], self::RESPONSE_BODY));
    }
}
