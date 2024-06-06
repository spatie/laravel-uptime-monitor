<?php

namespace Spatie\UptimeMonitor\Test\Integration\Helpers;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Exceptions\InvalidPeriod;
use Spatie\UptimeMonitor\Helpers\Period;
use Spatie\UptimeMonitor\Test\TestCase;

class PeriodTest extends TestCase
{
    /** @test */
    public function it_will_throw_an_exception_when_the_start_date_comes_after_the_end_date()
    {
        $this->expectException(InvalidPeriod::class);

        new Period(Carbon::now(), Carbon::now()->subMinutes(1));
    }

    /**
     * @test
     *
     * @dataProvider periodDataProvider
     */
    public function it_can_generate_a_string_representation_of_the_duration(int $differenceInMinutes, string $formattedString)
    {
        $period = new Period(Carbon::now(), Carbon::now()->addMinutes($differenceInMinutes));

        $this->assertEquals($formattedString, $period->duration());
    }

    public static function periodDataProvider(): array
    {
        return [
            [10, '10m'],
            [59, '59m'],
            [60, '1h 0m'],
            [61, '1h 1m'],
            [100, '1h 40m'],
            [1000, '16h 40m'],
            [1440, '1d 0h 0m'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider textDataProvider
     */
    public function it_has_a_text_representation(Carbon $startDateTime, Carbon $endDateTime, string $text)
    {
        $period = new Period($startDateTime, $endDateTime);

        $this->assertEquals($text, $period->toText());
    }

    public static function textDataProvider(): array
    {
        Carbon::setTestNow(Carbon::create(2016, 1, 1, 00, 00, 00));

        return [
            [Carbon::now(), Carbon::now()->addMinutes(10), '00:00 ➡️ 00:10'],
            [Carbon::now()->subMinutes(10), Carbon::now(), '23:50 on 31/12/2015 ➡️ 00:00'],
            [Carbon::now()->subHour(1), Carbon::now()->subMinutes(10), '23:00 on 31/12/2015 ➡️ 23:50 on 31/12/2015'],
        ];
    }
}
