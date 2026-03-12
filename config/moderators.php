<?php

use App\Components\Moderator\Factories\CommentModeratorsFactory;
use App\Components\Moderator\Factories\ContactModeratorsFactory;
use App\Components\Moderator\Factories\ModeratorsConfigFactory;
use App\Components\Moderator\Factories\ModeratorsFallbackFactory;
use App\Components\Moderator\Factories\ModeratorsFileFactory;
use App\Components\Moderator\Moderators\Comments\EmailModerator;
use App\Components\Moderator\Moderators\Comments\LanguageModerator;
use App\Components\Moderator\Moderators\Comments\LinkModerator;
use App\Components\Moderator\Moderators\Comments\ProfanityModerator;
use App\Components\Moderator\Moderators\Contact\BlacklistModerator;

return [
    // Comments builder configuration
    'comments' => [
        // Factory class for creating database moderators
        'factory' => CommentModeratorsFactory::class,

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
                    'moderator' => ProfanityModerator::class,

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
                            'source' => 'require',
                            'path' => app_path('Components/Moderator/data/profanity.php'),
                            'key' => 'en',
                        ],
                    ],
                ],

                [
                    // Language moderator configuration
                    'moderator' => LanguageModerator::class,

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
                    'moderator' => EmailModerator::class,

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
                    'moderator' => LinkModerator::class,

                    'enabled' => true,
                ],
            ],
        ],
    ],

    // Contact builder configuration
    'contact' => [
        // Factory class for creating database moderators
        'factory' => ContactModeratorsFactory::class,

        'options' => [
            'moderators' => [
                [
                    // Blacklist moderator configuration
                    'moderator' => BlacklistModerator::class,

                    'enabled' => true,

                    'ignoreCase' => true,
                ],
            ],
        ],
    ],

    // Fallback builder configuration
    'fallback' => [
        // Factory class for creating fallback moderators
        'factory' => ModeratorsFallbackFactory::class,

        'stack' => [
            'file',
            'config',
        ],
    ],

    // Config builder configuration
    'config' => [
        // Factory class for creating config-based moderators
        'factory' => ModeratorsConfigFactory::class,

        'options' => [],
    ],

    // File builder configuration
    'file' => [
        // Factory class for creating file-based moderators
        'factory' => ModeratorsFileFactory::class,

        'options' => [
            'disk' => 'local',
            'path' => 'data/moderators.json',
        ],
    ],
];
