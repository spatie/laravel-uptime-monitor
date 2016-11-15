<?php

namespace Spatie\UptimeMonitor\Test\Integration\Models\Traits;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\Test\TestCase;

class SupportsUptimeCheckTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Site */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create(['uptime_last_check_date' => Carbon::now()]);
    }

    /** @test */
    public function it_will_determine_that_a_site_most_be_rechecked_after_the_specified_amount_of_minutes()
    {
        $this->assertFalse($this->site->shouldCheckUptime());

        $this->progressMinutes(config('laravel-uptime-monitor.uptime_check.run_interval_in_minutes') -1);

        $this->assertFalse($this->site->shouldCheckUptime());

        $this->progressMinutes(1);

        $this->assertTrue($this->site->shouldCheckUptime());
    }

    /** @test */
    public function it_will_determine_that_a_site_that_is_down_must_always_be_checked()
    {
        $this->site->uptime_status = UptimeStatus::DOWN();
        $this->site->save();

        foreach(range(1, 10) as $index) {
            $this->assertTrue($this->site->shouldCheckUptime());

            $this->progressMinutes(1);
        }
    }

    /** @test */
    public function it_will_determine_that_a_site_that_is_not_enabled_must_never_be_checked()
    {
        $this->site->enabled = false;
        $this->site->save();

        foreach(range(1, 10) as $index) {
            $this->assertFalse($this->site->shouldCheckUptime());

            $this->progressMinutes(1);
        }
    }
}