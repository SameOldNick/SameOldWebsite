<?php

return [
    'authenticator' => [
        'driver' => 'totp',

        'drivers' => [
            'totp' => [

            ],
            'backup' => [
                /**
                 * How many backup codes to generate.
                 */
                'codes' => 6,
            ],
        ],
    ],

    'persist' => [
        'driver' => 'session',

        'drivers' => [
            'session' => [
                /**
                 * How long MFA is valid for in seconds.
                 * 0 means no expiry.
                 */
                'expiry' => 0,
            ],
        ],
    ],
];
