<?php

namespace Tests\Feature\Main\Blog;

use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentApproved;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\CommentPosted;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests a guest tries to post a comment.
     *
     * @return void
     */
    public function testGuestPostComment() {
        Event::fake();

        $article = Article::factory()->create();

        $text = $this->faker()->paragraphs(3, true);

        $this->assertThrows(
            fn () => $this->assertGuest()->withoutExceptionHandling()->post(route('blog.comment', ['article' => $article]), ['comment' => $text]),
            AuthenticationException::class
        );

        $this->assertDatabaseMissing(Comment::class, [
            'comment' => $text,
        ]);

        Event::assertNotDispatched(CommentCreated::class);
        Event::assertNotDispatched(CommentApproved::class);
    }

    /**
     * Test the comment is posted.
     */
    public function testPostComment()
    {
        Event::fake();

        // Test setup: Create an article
        $article = Article::factory()->create();

        // Authenticate a user and test the comment method with valid input
        $user = User::factory()->create();

        $text = $this->faker()->paragraphs(3, true);

        $response = $this->actingAs($user)
                        ->post(route('blog.comment', ['article' => $article]), ['comment' => $text]);

        // Expectation: A new comment is created, and CommentCreated event is dispatched.
        $response->assertRedirectToRoute('blog.single', ['article' => $article]);

        $this->assertDatabaseHas(Comment::class, [
            'comment' => $text,
        ]);

        Event::assertDispatched(CommentCreated::class);
        Event::assertNotDispatched(CommentApproved::class);
    }

    /**
     * Test the comment is posted and auto approved.
     */
    public function testPostCommentAutoApproved()
    {
        Event::fake();

        config(['blog.comments.require_approval' => false]);

        // Test setup: Create an article and user
        $article = Article::factory()->create();
        $user = User::factory()->create();

        // Authenticate a user and test the comment method with valid input
        $text = $this->faker()->paragraphs(3, true);

        $response = $this->actingAs($user)
                        ->post(route('blog.comment', ['article' => $article]), ['comment' => $text]);

        // Expectation: A new comment is created, and CommentCreated and CommentApproved events are dispatched.
        $response->assertRedirectToRoute('blog.single', ['article' => $article]);

        $this->assertDatabaseHas(Comment::class, [
            'comment' => $text,
        ]);

        Event::assertDispatched(CommentCreated::class);
        Event::assertDispatched(CommentApproved::class);
    }

    /**
     * Tests a gust replies to a comment.
     *
     * @return void
     */
    public function testGuestReplyToComment() {
        Event::fake();

        $user = User::factory()->create();
        $article = Article::factory()->create();
        $parent = Comment::factory()->hasPostWithUser($user)->state([
            'article_id' => $article
        ])->create();

        $text = $this->faker()->paragraphs(3, true);

        $this->assertThrows(
            fn () => $this->assertGuest()->withoutExceptionHandling()->post(route('blog.comment.reply-to', ['article' => $article, 'parent' => $parent]), ['comment' => $text]),
            AuthenticationException::class
        );

        $this->assertDatabaseMissing(Comment::class, [
            'comment' => $text,
        ]);

        Event::assertNotDispatched(CommentCreated::class);
        Event::assertNotDispatched(CommentApproved::class);
    }

    /**
     * Test the replyTo method to ensure that a user can submit
     * a reply to a comment for an article and events are dispatched.
     */
    public function testReplyToComment()
    {
        Event::fake();

        // Test setup: Create a user, article and a parent comment
        $user = User::factory()->create();
        $article = Article::factory()->create();
        $parent = Comment::factory()->hasPostWithUser($user)->state([
            'article_id' => $article
        ])->create();

        // Authenticate a user and test the replyTo method with valid input
        $text = $this->faker()->paragraphs(3, true);

        $response = $this->actingAs($user)
                        ->post(route('blog.comment.reply-to', ['article' => $article, 'parent' => $parent]), ['comment' => $text]);

        // Expectation: A new reply is created, and CommentCreated event is dispatched
        $response->assertRedirectToRoute('blog.single', ['article' => $article]);

        $this->assertDatabaseHas(Comment::class, [
            'parent_id' => $parent->getKey(),
            'comment' => $text,
        ]);

        Event::assertDispatched(CommentCreated::class);
        Event::assertNotDispatched(CommentApproved::class);
    }

    /**
     * Test the replyTo method to ensure that a user can submit
     * a reply to a comment for an article and events are dispatched.
     */
    public function testReplyToCommentAutoApproved()
    {
        Event::fake();

        config(['blog.comments.require_approval' => false]);

        // Test setup: Create a user, article and a parent comment
        $user = User::factory()->create();
        $article = Article::factory()->create();
        $parent = Comment::factory()->hasPostWithUser($user)->state([
            'article_id' => $article
        ])->create();

        // Authenticate a user and test the replyTo method with valid input
        $text = $this->faker()->paragraphs(3, true);

        $response = $this
                        ->actingAs($user)
                        ->post(
                            route('blog.comment.reply-to', ['article' => $article, 'parent' => $parent]),
                            ['comment' => $text]
                        );

        // Expectation: A new reply is created, CommentCreated and CommentApproved events are dispatched
        $response->assertRedirectToRoute('blog.single', ['article' => $article]);

        $this->assertDatabaseHas(Comment::class, [
            'parent_id' => $parent->getKey(),
            'comment' => $text,
        ]);

        Event::assertDispatched(CommentCreated::class);
        Event::assertDispatched(CommentApproved::class);
    }

    /**
     * Tests the article author is notified that a comment was posted.
     *
     * @return void
     */
    public function testArticleAuthorNotifiedOfComment() {
        Notification::fake();

        config(['blog.comments.require_approval' => false]);

        // Test setup: Create an article and user
        $article = Article::factory()->hasPostWithUser()->create();
        $user = User::factory()->create();

        // Authenticate a user and test the comment method with valid input
        $text = $this->faker()->paragraphs(3, true);

        $response = $this->actingAs($user)
                        ->post(route('blog.comment', ['article' => $article]), ['comment' => $text]);

        // Expectation: Article author is notified of comment.
        Notification::assertSentTo(
            [$article->post->user], CommentPosted::class
        );
    }

    /**
     * Tests commentors are notified of reply to comment.
     *
     * @return void
     */
    public function testReplyToNotifiesCommentors() {
        Notification::fake();

        config(['blog.comments.require_approval' => false]);

        // Test setup: Create an article and user
        $createUserCb = fn () => User::factory()->create();

        [$user1, $user2, $user3] = [$createUserCb(), $createUserCb(), $createUserCb()];

        $article = Article::factory()->hasPostWithUser()->create();

        $first = Comment::factory()->hasPostWithUser($user1)->state([
            'article_id' => $article
        ])->create();

        $second = Comment::factory()->hasPostWithUser($user2)->state([
            'article_id' => $article,
            'parent_id' => $first
        ])->create();

        // Authenticate a user and test the comment method with valid input
        $text = $this->faker()->paragraphs(3, true);

        $response = $this
                        ->actingAs($user3)
                        ->post(
                            route('blog.comment.reply-to', ['article' => $article, 'parent' => $second]),
                            ['comment' => $text]
                        );

        // Expectation: All users in thread (except commenter poster) are notified of comment.
        Notification::assertSentToTimes($user1, CommentPosted::class, 1);
        Notification::assertSentToTimes($user2, CommentPosted::class, 1);

        Notification::assertNotSentTo([$user3], CommentPosted::class);
    }
}
