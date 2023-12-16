<?php

namespace App\Http\Controllers\Api;

use App\Components\Analytics\AnalyticsRequest;
use App\Components\Analytics\ChartManager;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly ChartManager $chartManager
    ) {
    }

    /**
     * Gets the visitors over time.
     *
     * @param AnalyticsRequest $request
     * @return array
     */
    public function visitors(AnalyticsRequest $request)
    {
        $chart = $this->chartManager->create('dashboard.visitors');

        $data = $chart->generate();

        return $data;
    }

    /**
     * Gets the popular links.
     *
     * @param AnalyticsRequest $request
     * @return array
     */
    public function links(AnalyticsRequest $request)
    {
        $chart = $this->chartManager->create('dashboard.links');

        $data = $chart->generate();

        return $data;
    }

    /**
     * Gets the web browsers.
     *
     * @param AnalyticsRequest $request
     * @return array
     */
    public function browsers(AnalyticsRequest $request)
    {
        $chart = $this->chartManager->create('dashboard.browsers');

        $data = $chart->generate();

        return $data;
    }

    public function recentActivity()
    {
        // Get recent comments

        // Get recent blog articles
    }
}
