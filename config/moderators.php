<?php

return [
    // Default builder to use for moderation
    'builder' => 'database',

    'builders' => [
        // Database builder configuration
        'database' => [
            // Factory class for creating database moderators
            'factory' => \App\Components\Moderator\Factories\ModeratorsDatabaseFactory::class,

            'options' => [
                /**
                 * Filters to enable if unable to determine from database.
                 */
                'fallback' => [
                    'profanity',
                    'email',
                    'language',
                    'link',
                ],
            ],
        ],

        // Fallback builder configuration
        'fallback' => [
            // Factory class for creating fallback moderators
            'factory' => \App\Components\Moderator\Factories\ModeratorsFallbackFactory::class,

            'stack' => [
                'file',
                'config',
            ],
        ],

        // Config builder configuration
        'config' => [
            // Factory class for creating config-based moderators
            'factory' => \App\Components\Moderator\Factories\ModeratorsConfigFactory::class,

            'options' => [
                'moderators' => [
                    [
                        // Profanity moderator configuration
                        'moderator' => \App\Components\Moderator\Moderators\ProfanityModerator::class,

                        'enabled' => true,

                        'languages' => [
                            'en',
                        ],

                        /**
                         * What to replace profanity with
                         */
                        'mask' => '[redacted]',

                        'lists' => [
                            [
                                'source' => 'config',
                                'key' => 'profanity.en',
                            ],
                        ],
                    ],

                    [
                        // Language moderator configuration
                        'moderator' => \App\Components\Moderator\Moderators\LanguageModerator::class,

                        'enabled' => true,

                        'reason' => 'Comments are restricted to the English language.',

                        /**
                         * Allowed languages.
                         *
                         * @see LanguageDetector\LanguageDetector
                         */
                        'allowed' => [
                            'en',
                        ],
                    ],

                    [
                        // Email moderator configuration
                        'moderator' => \App\Components\Moderator\Moderators\EmailModerator::class,

                        'enabled' => true,

                        'allow' => [
                            [
                                'source' => 'inline',
                                'list' => [
                                    'gmail.com',
                                    'outlook.com',
                                    'hotmail.com',
                                ],
                            ],
                        ],

                        'deny' => [
                            [
                                'source' => 'storage',
                                'disk' => 'local',
                                'path' => 'data/disposable-emails.json',
                                'format' => 'json',
                            ],
                        ],
                    ],

                    [
                        // Link moderator configuration
                        'moderator' => \App\Components\Moderator\Moderators\LinkModerator::class,

                        'enabled' => true,
                    ],
                ],
            ],
        ],

        // File builder configuration
        'file' => [
            // Factory class for creating file-based moderators
            'factory' => \App\Components\Moderator\Factories\ModeratorsFileFactory::class,

            'options' => [
                'disk' => 'local',
                'path' => 'data/moderators.json',
            ],
        ],
    ],
];
