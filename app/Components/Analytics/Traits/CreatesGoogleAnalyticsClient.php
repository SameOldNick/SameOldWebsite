<?php

namespace App\Components\Analytics\Traits;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;

trait CreatesGoogleAnalyticsClient {
    protected function getPropertyId()
    {
        return config('services.google.analytics.property_id');
    }

    protected function createDataClient() {
        return new BetaAnalyticsDataClient([
            'credentials' => base_path(config('services.google.analytics.credentials')),
        ]);
    }
}
