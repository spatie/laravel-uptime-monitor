<?php

namespace Spatie\UptimeMonitor\Test\Notifications;

use Spatie\UptimeMonitor\Test\TestCase;

class EventHandlerTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site  */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }


}