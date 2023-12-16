<?php

namespace App\Components\Analytics\Charts;

use App\Components\Analytics\DateRangeHelper;
use App\Components\Analytics\Traits\CreatesGoogleAnalyticsClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Illuminate\Support\Carbon;

class VisitorsOverTimeChart extends Chart
{
    use CreatesGoogleAnalyticsClient;

    const DATETIME_FORMAT = 'Y-m-d';

    public function __construct(
        protected readonly DateRangeHelper $dateRangeHelper
    ) {
    }

    public function generate()
    {
        $client = $this->createDataClient();

        // Make an API call.
        $response = $client->runReport($this->createReportRequest());

        return $this->processResponse($response);
    }

    protected function getStartDate()
    {
        return $this->dateRangeHelper->getPeriod()->getStartDate();
    }

    protected function getEndDate()
    {
        return $this->dateRangeHelper->getPeriod()->getEndDate();
    }

    protected function createReportRequest()
    {
        // See https://developers.google.com/analytics/devguides/reporting/data/v1/api-schema for metrics and dimensions
        $request = (new RunReportRequest())
            ->setProperty(sprintf('properties/%s', $this->getPropertyId()))
            ->setKeepEmptyRows(true)
            ->setDateRanges([
                new DateRange([
                    'start_date' => $this->getStartDate()->format(static::DATETIME_FORMAT),
                    'end_date' => $this->getEndDate()->format(static::DATETIME_FORMAT),
                ]),
            ])
            ->setDimensions([
                new Dimension([
                    'name' => 'date',
                ]),
            ])
            ->setMetrics([
                new Metric(['name' => 'newUsers']),
                new Metric(['name' => 'totalUsers']),
            ]);

        return $request;
    }

    protected function processResponse(RunReportResponse $response)
    {
        foreach ($response->getRows() as $row) {
            $dimensions = $row->getDimensionValues();
            $metrics = $row->getMetricValues();

            $date = Carbon::createFromFormat('Ymd', $dimensions[0]->getValue())->startOfDay();

            $newUsers = $metrics[0]->getValue();
            $totalUsers = $metrics[1]->getValue();

            if ($this->dateRangeHelper->isWithinRange($date)) {
                $key = $this->dateRangeHelper->findClosestKey($date, true);

                $this->dateRangeHelper->setValue($key, fn ($old) => [
                    'newUsers' => $old['newUsers'] + $newUsers,
                    'totalUsers' => $old['totalUsers'] + $totalUsers,
                ]);
            }
        }

        return $this->dateRangeHelper->toArray();
    }
}
