<?php

return [
    /**
     * The default driver to use.
     */
    'default' => 'recaptcha',

    'drivers' => [
        'recaptcha' => [
            /**
             * The site key used to display the reCAPTCHA widget.
             *
             * @see https://developers.google.com/recaptcha/docs/v3
             */
            'site_key' => env('RECAPTCHA_SITE_KEY', ''),

            /**
             * The secret key used to verify the response.
             * This key should be kept secret.
             *
             * @see https://developers.google.com/recaptcha/docs/verify
             */
            'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

            /**
             * The minimum score required for the verification to pass.
             */
            'minimum_score' => env('RECAPTCHA_MINIMUM_SCORE', 0.5),

            /**
             * List of IP addresses that are allowed to bypass the verification.
             * 
             * Possible array values:
             *  - Exact IP address: 192.168.0.1
             *  - IP address range: 192.168.0.1/24
             *  - IP address with wildcard: 192.168.*.*
             */
            'exclude_ips' => [],

            /**
             * Additional options to pass to the Guzzle HTTP client.
             *
             * @see https://docs.guzzlephp.org/en/stable/request-options.html
             */
            'client_options' => [],
        ],
    ],
];
