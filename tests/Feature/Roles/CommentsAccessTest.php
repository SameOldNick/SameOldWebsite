<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class CommentsAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Tests user is authorized to get comments.
     *
     * @return void
     */
    public function testCanGetComments(): void
    {
        Comment::factory(5)->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->getJson('/api/blog/comments');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get comments.
     *
     * @return void
     */
    public function testCannotGetComments(): void
    {
        $response = $this->withRoles([])->getJson('/api/blog/comments');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get comment.
     *
     * @return void
     */
    public function testCanGetComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->getJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get comment.
     *
     * @return void
     */
    public function testCannotGetComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update comments.
     *
     * @return void
     */
    public function testCanUpdateComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->putJson(sprintf('/api/blog/comments/%d', $comment->getKey()), [
            'comment' => $this->faker()->realText(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to update comments.
     *
     * @return void
     */
    public function testCannotUpdateComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles([])->putJson(sprintf('/api/blog/comments/%d', $comment->getKey()), [
            'comment' => $this->faker()->realText(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete comments.
     *
     * @return void
     */
    public function testCanDeleteComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->deleteJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete comments.
     *
     * @return void
     */
    public function testCannotDeleteComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to approve comments.
     *
     * @return void
     */
    public function testCanApproveComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->postJson(sprintf('/api/blog/comments/%d/approve', $comment->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to approve comments.
     *
     * @return void
     */
    public function testCannotApproveComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles([])->postJson(sprintf('/api/blog/comments/%d/approve', $comment->getKey()));

        $response->assertForbidden();
    }
}
