<?php

namespace Spatie\UptimeMonitor\Test\Models;

use Spatie\UptimeMonitor\Exceptions\CannotSaveSite;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class SiteTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create(['url' => 'http://mysite.com']);
    }

    /** @test */
    public function it_will_throw_an_exception_when_creating_a_site_that_already_exists()
    {
        $this->expectException(CannotSaveSite::class);

        factory(Site::class)->create(['url' => 'http://mysite.com']);
    }

    /** @test */
    public function it_will_throw_an_exception_when_updating_a_url_to_an_url_of_a_site_that_already_exists()
    {
        $site = factory(Site::class)->create(['url' => 'http://myothersite.com']);

        $this->expectException(CannotSaveSite::class);

        $site->url = 'http://mysite.com';

        $site->save();
    }
}