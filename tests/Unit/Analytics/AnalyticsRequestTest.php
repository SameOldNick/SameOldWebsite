<?php

namespace Tests\Unit\Analytics;

use App\Components\Analytics\AnalyticsRequest;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AnalyticsRequestTest extends TestCase
{
    /**
     * Tests a range for 5 years with 30 entries is created.
     */
    public function test_five_year_range_created(): void
    {
        $expectedEntries = 30;
        $expectedGroupSize = 2;
        $startDate = Carbon::make('-5 years');
        $endDate = Carbon::make('now');

        $request = new AnalyticsRequest([
            'start' => $startDate,
            'end' => $endDate,
        ]);

        $period = $request->createPeriod($expectedEntries);

        // Assert there's # of expected entries.
        $this->assertEquals($expectedEntries, count($period));

        $range = $period->toArray();

        $first = current($range);
        $last = end($range);

        // Assert difference between entries is 2 months
        for ($i = 1; $i < count($range); $i++) {
            $a = $range[$i - 1];
            $b = $range[$i];

            $difference = (int) $a->diffInMonths($b);

            $this->assertEquals($expectedGroupSize, $difference);
        }

        // Assert first entry is start date.
        $this->assertTrue($first->isSameDay($startDate));

        // Assert last entry is end date.
        $this->assertTrue($last->isSameDay($endDate));
    }

    /**
     * Tests a range for 1 years with 30 entries is created.
     */
    public function test_one_year_range_created(): void
    {
        $expectedGroups = 30;
        $expectedGroupSize = round(365 / $expectedGroups);
        $startDate = Carbon::make('-365 days');
        $endDate = Carbon::make('now');

        $request = new AnalyticsRequest([
            'start' => $startDate,
            'end' => $endDate,
        ]);

        $period = $request->createPeriod($expectedGroups);

        // Assert there's # of expected entries.
        $this->assertEquals($expectedGroups, count($period));

        $range = $period->toArray();

        $first = current($range);
        $last = end($range);

        // Assert difference between entries is 2 months
        for ($i = 1; $i < count($range); $i++) {
            $a = $range[$i - 1];
            $b = $range[$i];

            $difference = (int) $a->diffInDays($b);

            $this->assertEquals($expectedGroupSize, $difference);
        }

        // Assert first entry is start date.
        $this->assertTrue($first->isSameDay($startDate));

        // Assert last entry is end date.
        $this->assertTrue($last->isSameDay($endDate));
    }
}
