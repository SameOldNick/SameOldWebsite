<?php

return [
    'driver' => 'geoip2',

    'drivers' => [
        'geoip2' => [
            'type' => 'city',
            'license' => env('GEOLOCATE_GEOIP2_LICENSE'),
            'paths' => [
                'country' => env('GEOLOCATE_GEOIP2_COUNTRY_PATH'),
                'city' => env('GEOLOCATE_GEOIP2_CITY_PATH'),
            ],
        ],
    ],
];
