<?php

return [
    'watchdog' => 'stack',
    'clerk' => 'stack',
    'responder' => 'stack',

    'watchdogs' => [
        'stack' => [
            'driver' => 'stack',

            'stack' => [
                'composer-audit',
                'https',
            ],
        ],

        'composer-audit' => [
            'driver' => 'composer-audit',

            'advisories' => [
                /**
                 * Whether to check for security advisories.
                 */
                'enabled' => true,

                /**
                 * Level of importance for security advisories.
                 * Possible values: 'low', 'medium', 'high', or 'critical'
                 */
                'level' => 'high',
            ],

            'abandoned' => [
                /**
                 * Whether to check for abandoned packages.
                 */
                'enabled' => true,

                /**
                 * Level of importance for abandoned packages.
                 * Possible values: 'low', 'medium', 'high', or 'critical'
                 */
                'level' => 'medium',
            ],
        ],

        'https' => [
            'driver' => 'https',

            /**
             * Level of importance for unsecure HTTP.
             */
            'level' => 'high',

            /**
             * URL to check.
             * If null, the URL generated by "secure_url('')" is used.
             */
            'url' => env('APP_SECURE_URL'),
        ],
    ],

    'clerks' => [
        'stack' => [
            'driver' => 'stack',

            'stack' => [
                'eloquent',
                'notification',
            ],
        ],

        'eloquent' => [
            'driver' => 'eloquent',
        ],

        'notification' => [
            'driver' => 'notification',
            'role' => 'admin',
        ],
    ],

    'responders' => [
        'stack' => [
            'driver' => 'stack',

            'stack' => [
                'event',
            ],
        ],

        'event' => [
            'driver' => 'event',
        ],

    ],

];
