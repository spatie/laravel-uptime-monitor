<?php

namespace Spatie\UptimeMonitor\Test\Integration\Helpers;

use Carbon\Carbon;
use Spatie\UptimeMonitor\Helpers\Period;
use Spatie\UptimeMonitor\Test\TestCase;

class PeriodTest extends TestCase
{
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

    public function periodDataProvider(): array
    {
        return [
          ['10', '0h 10m'],
          ['59', '0h 59m'],
          ['60', '1h 0m'],
          ['61', '1h 1m'],
          ['100', '1h 40m'],
          ['1000', '16h 40m'],
        ];
    }
}