<?php

namespace Spatie\UptimeMonitor\Models\Enums;

use MyCLabs\Enum\Enum;

class UptimeStatus extends Enum
{
    const NOT_YET_CHECKED = 'not yet checked';
    const UP = 'up';
    const DOWN = 'down';
}
