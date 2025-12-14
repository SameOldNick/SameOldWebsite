<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'analytics' => [
            'property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID', ''),
            'credentials' => env('GOOGLE_ANALYTICS_CREDENTIALS', ''),
        ],
    ],

    'ntfy' => [
        /**
         * Whether ntfy notifications are enabled.
         * This disables/enables the NtfyChannel. The Ntfy service can still be used directly.
         */
        'enabled' => env('NTFY_ENABLED', false),
        'server_url' => env('NTFY_SERVER_URL', 'https://ntfy.sh/'),
        /**
         * Auth method
         * Possible values: user, token, or null
         */
        'auth_method' => env('NTFY_AUTH_METHOD', null),
        'auth_credentials' => [
            'username' => env('NTFY_AUTH_USERNAME', ''),
            'password' => env('NTFY_AUTH_PASSWORD', ''),
        ],
        'auth_token' => env('NTFY_AUTH_TOKEN', ''),
        /**
         * The default topic to use if none is specified.
         */
        'default_topic' => env('NTFY_DEFAULT_TOPIC', null),
    ],
];
