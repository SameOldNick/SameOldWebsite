<?php

return [
    'key' => [
        /**
         * The type of key to sign/validate JWTs.
         * Options: secret, file, or none.
         */
        'default' => 'secret',

        /**
         * Configuration options for a secret key.
         */
        'secret' => [
            /**
             * The secret phrase to use.
             * Do not share this with anyone.
             * It's highly recommended you use the 'littlejwt:secret' artisan command to generate a secure phrase.
             * Changing the phrase will cause previously generated JWTs to be invalid.
             */
            'phrase' => env('LITTLEJWT_KEY_PHRASE', ''),

            /**
             * Whether to perform checks if phrase is not set or is empty.
             * It's NOT recommended to set this to true.
             */
            'allow_unsecure' => false,
        ],
        /**
         * Configurations options for a file key.
         */
        'file' => [
            /**
             * The type of file.
             * Options: pem, p12, or crt.
             */
            'type' => env('LITTLEJWT_KEY_FILE_TYPE', 'pem'),

            /**
             * Path to the key file.
             * Do not share this with anyone.
             * Changing the file will cause previously generated JWTs to be invalid.
             */
            'path' => env('LITTLEJWT_KEY_FILE_PATH', '/path/to/my/key/file.pem'),

            /**
             * The secret to use if the file is encrypted.
             * This does not apply to crt files.
             * Leave empty if file is not encrypted.
             */
            'secret' => env('LITTLEJWT_KEY_FILE_SECRET', ''),
        ],

        /**
         * The algorithm used to verify and sign JSON Web Tokens (JWTs).
         * @see https://web-token.spomky-labs.com/the-components/signed-tokens-jws/signature-algorithms List of supported algorithms
         */
        'algorithm' => \Jose\Component\Signature\Algorithm\HS256::class,
    ],

    'builder' => [
        /**
         * Mutators to use for claims in the header and payload.
         */
        'mutators' => [
            'header' => [],
            'payload' => [
                'iat' => 'timestamp',
                'nbf' => 'timestamp',
                'exp' => 'timestamp',
            ],
        ],
        /**
         * Indicates which claims should be added to the header or payload.
         */
        'claims' => [
            'header' => [
                'alg',
                'cty',
                'typ',
                'crit',
            ],
            'payload' => [

            ],
        ],
    ],
    'defaults' => [
        /**
         * The default buildable to use by createToken and createJWT in LittleJWT.
         */
        'buildable' => 'default',

        /**
         * The default validatable to use by validateToken and validateJWT in LittleJWT.
         */
        'validatable' => 'default',
    ],

    'buildables' => [
        'default' => [
            /**
             * Fully qualified buildable class to use.
             */
            'buildable' => \LittleApps\LittleJWT\Build\Buildables\DefaultBuildable::class,

            /**
             * Value to use for the 'alg' claim.
             */
            'alg' => 'HS256',

            /**
             * Number of seconds before JWT expires.
             */
            'ttl' => 3600,

            /**
             * Value to use for the 'iss' claim.
             */
            'iss' => env('APP_URL', 'http://localhost'),

            /**
             * Value to user for the 'aud' claim.
             */
            'aud' => env('APP_NAME', 'Laravel'),
        ],
    ],
    'validatables' => [
        'default' => [
            /**
             * Validatable instance to use for this validator.
             */
            'validatable' => \LittleApps\LittleJWT\Validation\Validatables\DefaultValidatable::class,

            /**
             * Claim keys required in the header and payload.
             */
            'required' => [
                'header' => ['alg'],
                'payload' => ['iss', 'iat', 'exp', 'nbf'],
            ],

            /**
             * Number of seconds to allow after JWT expiry date/time.
             */
            'leeway' => 0,

            /**
             * Expected value for the 'alg' claim.
             */
            'alg' => 'HS256',

            /**
             * Expected value for the 'iss' claim.
             */
            'iss' => env('APP_URL', 'http://localhost'),

            /**
             * Expected value for the 'aud' claim.
             */
            'aud' => env('APP_NAME', 'Laravel'),
        ],
        'guard' => [
            /**
             * Validatable instance to use for this validator.
             */
            'validatable' => \LittleApps\LittleJWT\Validation\Validatables\GuardValidatable::class,

            /**
             * If true, the guard validator checks that a user exists with the 'sub' claim identifier.
             * This is separate than the user provider retrieving the user to associate with the guard.
             */
            'exists' => true,

            /**
             * The expected value for the provider ('prv') payload claim.
             * If false, the 'prv' payload claim is not validated (not recommended).
             */
            'model' => \App\Models\User::class,
        ],
    ],
    /**
     * Configuration options for the LittleJWT guard.
     */
    'guard' => [
        /**
         * An adapter handles validating JWTs and is attached to the guard.
         */
        'adapters' => [
            'generic' => [
                /**
                 * The class for the adapter.
                 * This should not be changed.
                 */
                'adapter' => \LittleApps\LittleJWT\Guards\Adapters\GenericAdapter::class,
            ],
            'fingerprint' => [
                /**
                 * The class for the adapter.
                 * This should not be changed.
                 */
                'adapter' => \LittleApps\LittleJWT\Guards\Adapters\FingerprintAdapter::class,

                /**
                 * Name of the cookie to hold the fingerprint.
                 */
                'cookie' => 'fingerprint',

                /**
                 * How long the fingerprint cookie should live for (in minutes).
                 * If 0, the cookie has no expiry.
                 */
                'ttl' => 0,
            ],
        ],
    ],
    /**
     * Blacklist configuration options.
     */
    'blacklist' => [
        /**
         * Blacklist driver to use for storing blacklisted JWTs.
         */
        'driver' => 'cache',
        /**
         * Configuration options for cache driver.
         */
        'cache' => [
            /**
             * How long a JWT stays in the blacklist (in seconds).
             * Set to 0 to have JWTs blacklisted forever.
             */
            'ttl' => 0,
        ],
        /**
         * Configurations options for database driver.
         */
        'database' => [
            /**
             * Table to store blacklisted JWTs.
             */
            'table' => 'jwt_blacklist',

            'columns' => [
                /**
                 * The name of the column that holds the JWT identifier.
                 */
                'identifier' => 'jwt',

                /**
                 * The name of the column that holds the expiry date/time for the JWT.
                 */
                'expiry' => 'expires_at',
            ],
        ],
    ],

    /**
     * Configuration options to use with OpenSSL.
     * @see https://www.php.net/manual/en/function.openssl-csr-new.php The config parameter contains possible config options.
     */
    'openssl' => [
        'config' => env('LITTLEJWT_OPENSSL_CNF', ''),
    ],
];
