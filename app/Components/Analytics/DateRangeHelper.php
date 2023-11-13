<?php

namespace App\Components\Analytics;

use ArrayAccess;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonPeriodImmutable;
use Countable;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;

class DateRangeHelper implements ArrayAccess, Arrayable, Countable
{
    /**
     * Carbon Period instance
     *
     * @var CarbonPeriodImmutable
     */
    protected $period;

    /**
     * Gets the range
     *
     * @var array
     */
    protected $range;

    /**
     * Date/time format to use for each key.
     *
     * @var string
     */
    protected $dateTimeFormat = DateTime::ATOM;

    /**
     * Constructs DateRangeHelper
     *
     * @param DatePeriod|CarbonPeriod $period
     * @param mixed $initialValue The initial value for each date/time entry.
     */
    public function __construct($period, $initialValue = 0)
    {
        $this->period = CarbonPeriodImmutable::instance($period);
        $this->range = $this->generateRangeFromPeriod($this->period, $initialValue);
    }

    /**
     * Sets the date/time format.
     *
     * @param string $dateTimeFormat
     * @return $this
     */
    public function setDateTimeFormat(string $dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    /**
     * Gets the date/time format.
     *
     * @return string
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * Gets the date period.
     *
     * @return \DatePeriod
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Gets the date interval.
     *
     * @return \DateInterval
     */
    public function getInterval()
    {
        return $this->getPeriod()->getDateInterval();
    }

    /**
     * Gets the dates in the range.
     *
     * @return array
     */
    public function getDates()
    {
        return array_column($this->range, 0);
    }

    /**
     * Gets date/time at index.
     *
     * @param int $index
     * @return Carbon
     */
    public function getDate(int $index)
    {
        return $this->range[$index][0];
    }

    /**
     * Gets the start date/time.
     *
     * @return Carbon
     */
    public function getStart()
    {
        return $this->getDate(0);
    }

    /**
     * Gets the end date/time.
     *
     * @return Carbon
     */
    public function getEnd()
    {
        return $this->getDate($this->count() - 1);
    }

    /**
     * Gets the values for each date.
     *
     * @return array
     */
    public function getValues()
    {
        return array_column($this->range, 1);
    }

    /**
     * Checks if index exists.
     *
     * @param int $index
     * @return bool
     */
    public function has(int $index): bool
    {
        return isset($this->range[$index]);
    }

    /**
     * Gets the value at index.
     *
     * @param int $index
     * @return mixed
     */
    public function getValue(int $index)
    {
        return $this->range[$index][1];
    }

    /**
     * Sets the value at index
     *
     * @param int $index
     * @param mixed $value Static value or callable that receives existing value as argument.
     * @return $this
     */
    public function setValue(int $index, $value)
    {
        $value = value($value, $this->getValue($index));

        $this->range[$index][1] = $value;

        return $this;
    }

    /**
     * Checks if date/time is within range of this instance.
     *
     * @param DateTimeInterface $dateTime
     * @return bool
     */
    public function isWithinRange(DateTimeInterface $dateTime)
    {
        return $this->getStart()->lte($dateTime) && $this->getEnd()->gte($dateTime);
    }

    /**
     * Finds the key for the closest date/time.
     *
     * @param DateTimeInterface $dateTime
     * @param bool $closestBefore If true, the closest date/time must be before passed date/time.
     * @return int
     */
    public function findClosestKey(DateTimeInterface $dateTime, bool $closestBefore = false)
    {
        $differences = array_filter(
            array_map(
                fn ($value) => Carbon::instance($dateTime)->diffInRealSeconds($value, ! $closestBefore),
                $this->getDates()
            ),
            fn ($value) => $value >= 0
        );

        asort($differences);

        return key($differences);
    }

    /**
     * Formats the date/time as a string.
     *
     * @param DateTimeInterface $dateTime
     * @return string
     */
    protected function formatDateTime(DateTimeInterface $dateTime)
    {
        return $dateTime->format($this->getDateTimeFormat());
    }

    /**
     * Generates range of dates from a date period and sets the initial value for each.
     *
     * @param CarbonPeriod $period
     * @param mixed $initialValue
     * @return array
     */
    protected function generateRangeFromPeriod(CarbonPeriod $period, $initialValue)
    {
        $range = [];

        foreach ($period as $date) {
            array_push($range, [$date, $initialValue]);
        }

        return $range;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->getValue($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->setValue($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->range[$offset]);
    }

    /**
     * Gets the number of items in the range.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->range);
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        $data = [];

        foreach ($this->range as $columns) {
            [$dateTime, $value] = $columns;

            $key = $this->formatDateTime($dateTime);

            $data[$key] = $value;
        }

        return $data;
    }
}
