<?php

return [
    /*
     * Validation rules for password
     */
    'rules' => [
        /**
         * Passwords rules in production mode.
         */
        'production' => [
            /**
             * Minimum number of characters or 0 for no minimum.
             */
            'minimum' => 12,

            /**
             * Maximum number of characters or 0 for no maximum.
             */
            'maximum' => 0,

            /**
             * Required number of lowercase characters.
             */
            'lowercase' => 1,

            /**
             * Required number of uppercase characters.
             */
            'uppercase' => 1,

            /**
             * Required number of number characters.
             */
            'numbers' => 1,

            /**
             * Required number of special characters.
             */
            'special' => 1,

            /**
             * Only allow 7-bit ASCII characters.
             * Example: Ü, Ù, ù, ü, etc.
             */
            'ascii' => true,

            /**
             * Allow white-space characters.
             * Accepted values:
             *  - boolean: If true, any number of whitespaces are allowed. If false, no whitespaces are allowed.
             *  - array: An array of how many spaces, tabs, and newlines are allowed.
             */
            'whitespaces' => false,

            'blacklists' => [
                'blacklists' => ['common-passwords'],

                /**
                 * Deny substitutions for letters or characters.
                 * Example: "p@assw0rd" instead of "password"
                 */
                'substitutions' => true,
            ],
        ],

        /**
         * Passwords rules in development mode.
         */
        'development' => [
            /**
             * Minimum number of characters or 0 for no minimum.
             */
            'minimum' => 8,

            /**
             * Maximum number of characters or 0 for no maximum.
             */
            'maximum' => 0,

            /**
             * Required number of lowercase characters.
             */
            'lowercase' => 0,

            /**
             * Required number of uppercase characters.
             */
            'uppercase' => 0,

            /**
             * Required number of number characters.
             */
            'numbers' => 0,

            /**
             * Required number of special characters.
             */
            'special' => 0,

            /**
             * Only allow 7-bit ASCII characters.
             * Example: Ü, Ù, ù, ü, etc.
             */
            'ascii' => true,

            /**
             * Allow white-space characters.
             * Accepted values:
             *  - boolean: If true, any number of whitespaces are allowed. If false, no whitespaces are allowed.
             *  - array: An array of how many spaces, tabs, and newlines are allowed.
             */
            'whitespaces' => false,

            'blacklists' => [
                'blacklists' => [],

                /**
                 * Deny substitutions for letters or characters.
                 * Example: "p@assw0rd" instead of "password"
                 */
                'substitutions' => true,
            ],
        ],
    ],
];
