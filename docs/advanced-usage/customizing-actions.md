---
title: Customizing actions
weight: 9
---

The work of this package is performed by action classes. You can swap any of them for your own by registering your class in the `actions` key of the `uptime-monitor` config file.

```php
'actions' => [
    'send_uptime_check_request' => Spatie\UptimeMonitor\Actions\SendUptimeCheckRequestAction::class,
],
```

## Customizing how requests are made

`SendUptimeCheckRequestAction` builds the Guzzle client and sends the request for a monitor. Override it when you want full control over how requests are made.

The action has three methods you can override:

- `makeClient()`: returns the Guzzle client used for all monitors in a check run
- `requestOptions(Monitor $monitor)`: the options passed to the request of a single monitor
- `headers(Monitor $monitor)`: the headers passed to the request of a single monitor

Here's an example that turns off Guzzle's connection retries.

```php
namespace App\Actions;

use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\ClientInterface;
use Spatie\UptimeMonitor\Actions\SendUptimeCheckRequestAction;

class NoRetriesSendUptimeCheckRequestAction extends SendUptimeCheckRequestAction
{
    protected function makeClient(): ClientInterface
    {
        return GuzzleFactory::make(
            config('uptime-monitor.uptime_check.guzzle_options', []),
            config('uptime-monitor.uptime_check.retry_connection_after_milliseconds', 100),
            null,
            0
        );
    }
}
```

Register it in the config file.

```php
'actions' => [
    'send_uptime_check_request' => App\Actions\NoRetriesSendUptimeCheckRequestAction::class,
],
```

The action is resolved from the container, so you can typehint any dependency you need in its constructor.
