<?php

namespace Spatie\UptimeMonitor\Test\Integration\Models\Traits;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class SupportsUptimeCheckTest extends TestCase
{
    /** @var \Spatie\UptimeMonitor\Models\Monitor */
    protected $monitor;

    public function setUp()
    {
        parent::setUp();

        $this->monitor = factory(Monitor::class)->create(['uptime_last_check_date' => Carbon::now()]);
    }

    /** @test */
    public function it_will_determine_that_a_site_most_be_rechecked_after_the_specified_amount_of_minutes()
    {
        $this->assertFalse($this->monitor->shouldCheckUptime());

        $this->progressMinutes(config('laravel-uptime-monitor.uptime_check.run_interval_in_minutes') - 1);

        $this->assertFalse($this->monitor->shouldCheckUptime());

        $this->progressMinutes(1);

        $this->assertTrue($this->monitor->shouldCheckUptime());
    }

    /** @test */
    public function it_will_determine_that_a_site_that_is_down_must_always_be_checked()
    {
        $this->monitor->uptime_status = UptimeStatus::DOWN();
        $this->monitor->save();

        foreach (range(1, 10) as $index) {
            $this->assertTrue($this->monitor->shouldCheckUptime());

            $this->progressMinutes(1);
        }
    }

    /** @test */
    public function it_will_determine_that_a_site_that_is_not_enabled_must_never_be_checked()
    {
        $this->monitor->enabled = false;
        $this->monitor->save();

        foreach (range(1, 10) as $index) {
            $this->assertFalse($this->monitor->shouldCheckUptime());

            $this->progressMinutes(1);
        }
    }

    /** @test */
    public function it_will_set_uptime_status_last_change_date_when_the_status_changes()
    {
        $this->assertTrue($this->monitorAttributeIsSetToNow('uptime_status_last_change_date'));

        $this->progressMinutes(5);

        $this->assertFalse($this->monitorAttributeIsSetToNow('uptime_status_last_change_date'));

        $this->artisan('sites:check-uptime');

        $this->assertTrue($this->monitorAttributeIsSetToNow('uptime_status_last_change_date'));
    }

    /** @test */
    public function it_will_update_the_last_checked_date_no_matter_what_the_update_status_of_a_site_is()
    {
        foreach ([UptimeStatus::UP, UptimeStatus::DOWN, UptimeStatus::NOT_YET_CHECKED] as $status) {
            $this->monitor->uptime_status = $status;
            $this->monitor->save();

            foreach (range(1, 10) as $index) {
                $this->progressMinutes(10);
                $this->artisan('sites:check-uptime');

                $this->assertTrue($this->monitorAttributeIsSetToNow('uptime_last_check_date'));
            }
        }
    }

    protected function monitorAttributeIsSetToNow(string $attribute): bool
    {
        $this->monitor = $this->monitor->fresh();

        return $this->monitor->$attribute->diffInMinutes() === 0;
    }
}
