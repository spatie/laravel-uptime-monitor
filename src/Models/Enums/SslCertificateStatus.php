<?php

namespace Spatie\UptimeMonitor\Models\Enums;

use MyCLabs\Enum\Enum;

class SslCertificateStatus extends Enum
{
    const NOT_YET_CHECKED = 'not yet checked';
    const VALID = 'valid';
    const INVALID = 'invalid';
}
