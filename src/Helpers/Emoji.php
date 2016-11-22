<?php

namespace Spatie\UptimeMonitor\Helpers;

class Emoji
{
    public static function ok(): string
    {
        return "\u{2705}";
    }

    public static function notOk(): string
    {
        return "\u{274C}";
    }

    public static function rightwardsArrow(): string
    {
        return "\u{27A1}";
    }
}
