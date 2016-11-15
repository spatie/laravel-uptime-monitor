<?php

namespace Spatie\UptimeMonitor\Helpers;

class ConsoleOutput
{
    /** @var \Illuminate\Console\OutputStyle */
    public static $output;

    /**
     * @param $output
     */
    public function setOutput($output)
    {
        static::$output = $output;
    }

    public static function __callStatic(string $method, $arguments)
    {
        $consoleOutput = app(static::class);

        if (! $consoleOutput::$output) {
            var_dump('nope');
            return;
        }

        static::$output->$method(...$arguments);
    }
}
