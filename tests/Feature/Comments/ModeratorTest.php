<?php

namespace Tests\Feature\Comments;

use App\Components\Moderator\ModerationService;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModeratorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests profanity is detected
     */
    public function test_detects_profanity(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker()->profanity,
        ])->createPostWithGuestPerson()->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $flags = $moderator->moderate($comment);

        $comment->flags()->saveMany($flags);

        $this->assertNotEmpty($flags);
        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests comment is incorrect language.
     */
    public function test_comment_incorrect_language(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker('ar_SA')->realText,
        ])->createPostWithGuestPerson()->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $flags = $moderator->moderate($comment);

        $comment->flags()->saveMany($flags);

        $this->assertNotEmpty($flags);
        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests HTTP link is detected
     */
    public function test_detects_http_link(): void
    {
        $comment = Comment::factory([
            'comment' => $this->faker()->url,
        ])->createPostWithGuestPerson()->for(Article::factory())->create();

        $moderator = $this->app->make(ModerationService::class);

        $flags = $moderator->moderate($comment);

        $comment->flags()->saveMany($flags);

        $this->assertNotEmpty($flags);
        $this->assertTrue($comment->isFlagged());
    }

    /**
     * Tests comment is not flagged
     */
    public function test_comment_not_flagged(): void
    {
        $comment = Comment::factory()->for(Article::factory())->createPostWithGuestPerson()->create([
            'comment' => 'This is a test comment',
        ]);

        $moderator = $this->app->make(ModerationService::class);

        $flags = $moderator->moderate($comment);

        $comment->flags()->saveMany($flags);

        $this->assertEmpty($flags);
        $this->assertFalse($comment->isFlagged(), optional($comment->flags()->first())->reason ?? '');
    }
}
