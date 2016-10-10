<?php

use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

function uptimeMonitorConsoleOutput(): ConsoleOutput
{
    return app(ConsoleOutput::class);
}
