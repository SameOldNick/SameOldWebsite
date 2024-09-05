<?php

return [
    // Comments builder configuration
    'comments' => [
        // Factory class for creating database moderators
        'factory' => \App\Components\Moderator\Factories\CommentModeratorsFactory::class,

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

            'moderators' => [
                [
                    // Profanity moderator configuration
                    'moderator' => \App\Components\Moderator\Moderators\Comments\ProfanityModerator::class,

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
                    'moderator' => \App\Components\Moderator\Moderators\Comments\LanguageModerator::class,

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
                    'moderator' => \App\Components\Moderator\Moderators\Comments\EmailModerator::class,

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
                    'moderator' => \App\Components\Moderator\Moderators\Comments\LinkModerator::class,

                    'enabled' => true,
                ],
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

        'options' => [],
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
];
