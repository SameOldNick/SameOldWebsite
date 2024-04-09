<?php

namespace App\Components\Analytics\Traits;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;

trait CreatesCarbons
{
    /**
     * Determines appropriate interval unit.
     * This is useful in figuring out the best interval unit to pass to the range method to get the number of desired dates.
     *
     * @param CarbonInterface $from Start date/time
     * @param CarbonInterface $to End date/time
     * @param int $count How many dates there should be between from and to.
     * @return string Interval unit (year, month, week, etc.)
     */
    public function determineIntervalUnit(CarbonInterface $from, CarbonInterface $to, int $count)
    {
        $interval = $to->diffAsCarbonInterval($from);

        $secondsPerInterval = $interval->total('seconds') / $count;

        $units = [
            'year',
            'month',
            'week',
            'day',
            'hour',
            'minute',
            'second',
        ];

        // Determine if interval length is closest to year, month, week, etc.

        $differences = [];

        foreach ($units as $unit) {
            $difference = abs($secondsPerInterval - CarbonInterval::getFactor('seconds', $unit));

            $differences[$unit] = $difference;
        }

        $sorted = Arr::sort($differences);

        return array_keys($sorted)[0];
    }

    /**
     * Generates range of dates starting from date and going to date
     *
     * @param CarbonInterface $from Start date/time
     * @param CarbonInterface $to End date/time
     * @param string $unit How much dates should be incremented by (minutes, hours, days, etc.)
     * @param int $max Max. number of periods. 0 is unlimited. (default: 0)
     * @return array|false Array of dates or false if from, to, or unit is invalid.
     */
    public function createPeriods(CarbonInterface $from, CarbonInterface $to, string $unit, int $max = 0)
    {
        $secondsPerInterval = CarbonInterval::getFactor('seconds', $unit);
        $intervalCount = $to->diffAsCarbonInterval($from)->total('seconds') / $secondsPerInterval;

        if (is_null($secondsPerInterval) || $max > $intervalCount) {
            return false;
        }

        if ($max === 0) {
            $max = $intervalCount;
        }

        $periods = [];

        $start = $from;
        $n = 0;

        while ($start->lessThan($to) || $n < $max) {
            $end = Carbon::create($start)->addSeconds($secondsPerInterval)->subSecond();

            $period = $start->toPeriod($end, 1, $unit);

            array_push($periods, $period);

            $start = $start->addSeconds($secondsPerInterval);
            $n++;
        }

        return $periods;
    }

    /**
     * Creates CarbonPeriod instance
     *
     * @param integer $entries Number of entries in period.
     * @return CarbonPeriod
     */
    public function createPeriod(int $entries = 1)
    {
        /**
         * From the starting and ending date/time, we need to figure out how long
         * the interval (time between) needs to be in order for there to be
         * "count" number of dates in the period. If the start date/time is 5 years ago,
         * the end date/time is now, the count is 30 and the unit is "months", the
         * period will have just 2 dates: 5 years (60 months ago) and the date that's
         * 30 months ago.
         * We want the period to have the date every 6 months from 5 years ago.
         */

        // First, we need to figure out how many seconds there are between start and end
        $seconds = $this->start()->diffInSeconds($this->end());

        // Calculate the interval in seconds to have exactly $entries entries
        if ($entries > 1) {
            $interval = CarbonInterval::seconds($seconds / ($entries - 1)); // Subtract 1 to account for the initial date
        } else {
            // Don't allow dividing by zero
            $interval = CarbonInterval::seconds($seconds);
        }

        return $this->start()->toPeriod($this->end(), $interval);
    }
}
