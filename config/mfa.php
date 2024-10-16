<?php

return [
    'authenticator' => [
        /**
         * Default MFA driver.
         * Specifies the driver used for authentication. Typically, this would be a TOTP (Time-based One-Time Password) for apps like Google Authenticator.
         */
        'driver' => env('MFA_AUTH_DRIVER', 'totp'),

        /**
         * List of available MFA drivers.
         * Each driver can have its own configuration settings.
         */
        'drivers' => [
            'totp' => [
                // Currently, there are no configuration options for the TOTP driver.
            ],

            'backup' => [
                /**
                 * Number of backup codes to generate for MFA.
                 * These codes are used when the user cannot access their primary MFA method.
                 * Default: 6 backup codes.
                 */
                'codes' => env('MFA_AUTH_BACKUP_CODES', 6),
            ],
        ],

        /**
         * Routes to be registered for MFA drivers.
         * Specify which authentication methods should have routes automatically generated for them.
         * Available drivers: 'totp', 'backup'.
         */
        'routes' => [
            'totp',
            'backup',
        ],
    ],

    'persist' => [
        /**
         * Driver for persisting MFA sessions.
         * Defines how MFA session persistence is managed. The default is 'session', meaning the MFA state is stored in the session.
         */
        'driver' => env('MFA_PERSIST_DRIVER', 'session'),

        /**
         * Configuration for different persistence drivers.
         */
        'drivers' => [
            'session' => [
                /**
                 * MFA session expiration time (in seconds).
                 * Defines how long the MFA session is valid.
                 * If set to 0, the session will not expire and will persist until explicitly invalidated.
                 * Default: 0 (no expiry).
                 */
                'expiry' => env('MFA_PERSIST_SESSION_EXPIRY', 0),
            ],
        ],
    ],
];
