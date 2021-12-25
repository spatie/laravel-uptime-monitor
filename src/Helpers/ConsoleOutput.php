<?php

namespace Spatie\UptimeMonitor\Helpers;

use Illuminate\Console\Command;

class ConsoleOutput
{
    public static Command $runningCommand;

    public function setOutput(Command $runningCommand)
    {
        static::$runningCommand = $runningCommand;
    }

    public static function __callStatic(string $method, $arguments)
    {
        if (! static::$runningCommand) {
            return;
        }

        static::$runningCommand->$method(...$arguments);
    }
}
