<?php

namespace Spatie\UptimeMonitor\Exceptions;

use Exception;
use Spatie\UptimeMonitor\Models\Site;

class CannotSaveSite extends Exception
{
    public static function alreadyExists(Site $site)
    {
        return new static("Could not save a site with url `{$site->url}` because there already exists another site with the same url in the database. Try saving a site with a different url.");
    }
}