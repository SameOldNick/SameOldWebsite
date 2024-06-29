<?php

namespace Tests\Feature\Comments;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Factories\ModeratorsConfigFactory;
use App\Components\Moderator\Factories\ModeratorsDatabaseFactory;
use App\Components\Moderator\Factories\ModeratorsFileFactory;
use App\Components\Moderator\ModerationService;
use App\Components\Moderator\Moderators;
use App\Components\Settings\Facades\PageSettings;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModeratorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests moderation service is built from database
     */
    public function test_building_from_database(): void {
        Page::firstWhere('page', 'blog')->metaData()->updateOrCreate(
            ['key' => 'moderators'],
            ['value' => ['profanity']]
        );

        config(['moderators' => [
            'builder' => 'database',

            'builders' => [
                'database' => [
                    'factory' => ModeratorsDatabaseFactory::class,

                    'options' => [
                        /**
                        * Filters to enable if unable to determine from database.
                        */
                        'fallback' => [
                            'profanity',
                            'email',
                            'language',
                            'link'
                        ],
                    ],
                ],
                'config' => [
                    'factory' => ModeratorsConfigFactory::class,

                    'options' => [
                        'moderators' => [
                            [
                                'moderator' => Moderators\ProfanityModerator::class,

                                'enabled' => true,

                                'languages' => [
                                    'en'
                                ],

                                /**
                                * What to replace profanity with
                                */
                                'mask' => '[redacted]',

                                'lists' => [
                                    [
                                        'source' => 'config',
                                        'key' => 'profanity.en'
                                    ]
                                ]
                            ],

                            [
                                'moderator' => Moderators\LanguageModerator::class,

                                'enabled' => true,

                                'reason' => 'Comments are restricted to the English language.',

                                /**
                                * Allowed languages.
                                * @see LanguageDetector\LanguageDetector
                                */
                                'allowed' => [
                                    'en'
                                ]
                            ],
                        ],
                    ],
                ]
            ]
        ]]);

        /**
         * @var ModerationService $moderator
         */
        $moderator = $this->app->make(ModerationService::class);

        $this->assertInstanceOf(ModeratorsDatabaseFactory::class, $moderator->factory);

        $moderators = $moderator->getModerators();

        $this->assertCount(2, $moderators);
        $this->assertTrue(Arr::first($moderators, fn (Moderator $filter) => $filter instanceof Moderators\ProfanityModerator)->isEnabled());
        $this->assertFalse(Arr::first($moderators, fn (Moderator $filter) => $filter instanceof Moderators\LanguageModerator)->isEnabled());
    }

    /**
     * Tests moderation service is built from config
     */
    public function test_building_from_config(): void {
        config(['moderators' => [
            'builder' => 'config',

            'builders' => [
                'config' => [
                    'factory' => ModeratorsConfigFactory::class,

                    'options' => [
                        'moderators' => [],
                    ],
                ]
            ]
        ]]);

        /**
         * @var ModerationService $moderator
         */
        $moderator = $this->app->make(ModerationService::class);

        $this->assertEmpty($moderator->getModerators());
        $this->assertInstanceOf(ModeratorsConfigFactory::class, $moderator->factory);
    }

    /**
     * Tests moderation service is built from file
     */
    public function test_building_from_file(): void {
        Storage::fake();

        $disk = 'local';
        $path = 'data/moderators.json';

        // Get existing config options and store it as json file.
        Storage::disk($disk)->put($path, json_encode(config('moderators.builders.config.options', []), JSON_PRETTY_PRINT));

        config(['moderators' => [
            'builder' => 'file',

            'builders' => [
                'file' => [
                    'factory' => ModeratorsFileFactory::class,

                    'options' => [
                        'disk' => $disk,
                        'path' => $path
                    ]
                ]
            ]
        ]]);

        /**
         * @var ModerationService $moderator
         */
        $moderator = $this->app->make(ModerationService::class);

        $this->assertNotEmpty($moderator->getModerators());
        $this->assertInstanceOf(ModeratorsFileFactory::class, $moderator->factory);
    }

    /**
     * Tests profanity is detected
     */
    public function test_detects_profanity(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker()->profanity
        ])->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $moderator->moderate($comment);

        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests comment is incorrect language.
     */
    public function test_comment_incorrect_language(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker('ar_SA')->realText
        ])->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $moderator->moderate($comment);

        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests spam is detected
     */
    public function test_detects_spam(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker()->spam
        ])->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $moderator->moderate($comment);

        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests HTTP link is detected
     */
    public function test_detects_http_link(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker()->profanity
        ])->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $moderator->moderate($comment);

        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests comment is not flagged
     */
    public function test_comment_not_flagged(): void
    {
        $comment = Comment::factory()->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $moderator->moderate($comment);

        $this->assertFalse($comment->isFlagged(), optional($comment->flags()->first())->reason ?? '');
    }
}
