<?php

namespace App\Components\Analytics\Traits;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;

trait CreatesGoogleAnalyticsClient
{
    /**
     * Gets the Google Analytics property ID
     *
     * @return string
     */
    protected function getPropertyId()
    {
        return config('services.google.analytics.property_id');
    }

    /**
     * Creates Google Analytics data client.
     *
     * @return BetaAnalyticsDataClient
     */
    protected function createDataClient()
    {
        return new BetaAnalyticsDataClient($this->getDataClientOptions());
    }

    /**
     * Gets the options used to create Google Analytics data client.
     *
     * @return array
     */
    protected function getDataClientOptions() {
        return [
            'credentials' => base_path(config('services.google.analytics.credentials'))
        ];
    }
}
