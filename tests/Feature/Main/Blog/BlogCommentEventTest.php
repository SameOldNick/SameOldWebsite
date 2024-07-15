<?php

namespace Tests\Feature\Main\Blog;

use App\Components\Settings\Facades\PageSettings;
use App\Enums\CommentStatus;
use App\Events\Comments\CommentApproved;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Listeners\Comments\ModerateComment;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\FakesReCaptcha;
use Tests\TestCase;

class BlogCommentEventTest extends TestCase
{
    use CreatesUser;
    use FakesReCaptcha;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests approved event is fired when guest posts a comment
     */
    #[Test]
    public function guest_comment_approved_with_auto_moderation()
    {
        Event::fake();
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'auto',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);

        Event::assertDispatched(CommentCreated::class, fn (CommentCreated $event) => $event->comment->is($comment));
    }

    /**
     * Tests approved event is fired when guest posts a comment
     */
    #[Test]
    public function guest_comment_approved_with_manual_moderation()
    {
        Event::fake();
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'manual',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);

        Event::assertDispatched(CommentCreated::class, fn (CommentCreated $event) => $event->comment->is($comment));
        Event::assertNotDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment));
    }

    /**
     * Tests moderation is disabled when guest posts a comment
     */
    #[Test]
    public function guest_comment_approved_with_disabled_moderation()
    {
        Event::fakeExcept([CommentCreated::class]);
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'disabled',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);

        Event::assertDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment) && $event->comment->status === CommentStatus::Approved->value);
    }

    /**
     * Tests comment is approved when guest posts a comment
     */
    #[Test]
    public function registered_user_approved_with_auto_moderation()
    {
        Event::fakeExcept([CommentCreated::class]);
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'auto',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull($comment = Comment::owned($this->user)->first());

        Event::assertDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment) && $event->comment->status === CommentStatus::Approved->value);
    }

    /**
     * Tests events are dispatched after registered user posts comment with manual moderation.
     */
    #[Test]
    public function registered_user_approved_with_manual_moderation()
    {
        Event::fakeExcept([CommentCreated::class]);
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'manual',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull($comment = Comment::owned($this->user)->first());

        Event::assertNotDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment));
    }

    /**
     * Tests moderation is disabled when guest posts a comment
     */
    #[Test]
    public function registered_user_comment_approved_with_disabled_moderation()
    {
        Event::fakeExcept([CommentCreated::class]);
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'disabled',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::owned($this->user)->first();
        $this->assertNotNull($comment);

        Event::assertDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment) && $event->comment->status === CommentStatus::Approved->value);
    }

    /**
     * Tests events aren't fired when guest posts a comment
     */
    #[Test]
    public function guest_posts_comment_without_events()
    {
        Event::fake();
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'auto',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => null,
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNull($comment);

        Event::assertNotDispatched(CommentCreated::class, fn (CommentCreated $event) => $event->comment->is($comment));
        Event::assertNotDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment));
    }

    /**
     * Tests events aren't fired when registered user posts a comment
     */
    #[Test]
    public function registered_user_posts_comment_without_events()
    {
        Event::fake();
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'auto',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => null,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::owned($this->user)->first();
        $this->assertNull($comment);

        Event::assertNotDispatched(CommentCreated::class, fn (CommentCreated $event) => $event->comment->is($comment));
        Event::assertNotDispatched(CommentStatusChanged::class, fn (CommentStatusChanged $event) => $event->comment->is($comment));
    }
}
