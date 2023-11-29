<?php

return [
    'defaults' => [
        'signer' => 'ecdsa',
    ],

    'drivers' => [
        'ecdsa' => [
            /**
             * Type of key.
             * Possible values: 'file' or 'string'
             */
            'type' => env('ENCRYPTION_ECDSA_KEY_TYPE', 'file'),

            /**
             * Path to private key (if type is 'file').
             * The file contents must be in PEM format.
             */
            'path' => env('ENCRYPTION_ECDSA_KEY_PATH'),

            /**
             * Private key contents (if type is 'string').
             * The contents must be in PEM format.
             */
            'contents' => env('ENCRYPTION_ECDSA_KEY_CONTENTS'),

        ],
    ],
];
