---
title: Sending and verifying a payload
weight: 8
---

There are cases in which you would like to send a payload and verify the response to determine if is services is up and active for example, if you need to detrerming if a down stream service is connected and functioning correctly.

To achieve this you will need to manually update a few fields in the database and optionally create a custom response checker specifically for that monitor to verify the response from the uptime monitor request.

In this example, you will need to set the following fields in the database:

- `uptime_check_method`: `POST`
 - `uptime_check_payload`: `{"foo":"bar"}`
 - `uptime_check_additional_headers`: `{"Content-Type":"application/json"}`
 - `uptime_check_response_checker`: `App\ResponseCheckers\ExampleChecker`

 _More details on these fields can be found in the section "[Manually Modifying Monitors](/laravel-uptime-monitor/v3/advanced-usage/manually-modifying-monitors)"_

We will want to do some custom verification specifically for the response of this check.

Our checker could look something like the following:

```php
<?php

namespace App\ResponseCheckers;

use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Helpers\UptimeResponseCheckers\UptimeResponseChecker;

class ExampleChecker implements UptimeResponseChecker
{
    public function isValidResponse(ResponseInterface $response, Monitor $monitor) : bool
    {
        return $response->getStatusCode() === Response::HTTP_OK
            && (json_decode((string) $response->getBody(), true))['foo'] === 'bar';
    }

    public function getFailureReason(ResponseInterface $response, Monitor $monitor) : string
    {
        return vsprintf('Foo returned %s instead of bar with a status code of %s', [
            json_decode((string) $response->getBody(), true)['foo'],
            $response->getStatusCode()
        ]);
    }
}
```

This workflow is extremely flexible and should allow for some pretty advanced uptime and monitoring checks.
