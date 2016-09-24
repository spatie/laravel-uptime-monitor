<?php

namespace Spatie\UptimeMonitor\Helpers;

class Format
{
    public static function emoji(bool $bool): string
    {
        if ($bool) {
            return "\u{2705}";
        }

        return "\u{274C}";
    }
}
