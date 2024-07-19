<?php

namespace Tests\Feature\Roles;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class CommentsAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get comments.
     */
    public function testCanGetComments(): void
    {
        Comment::factory(5)->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->getJson('/api/blog/comments');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get comments.
     */
    public function testCannotGetComments(): void
    {
        $response = $this->withNoRoles()->getJson('/api/blog/comments');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get comment.
     */
    public function testCanGetComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->getJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get comment.
     */
    public function testCannotGetComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update comments.
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
     */
    public function testCannotUpdateComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withNoRoles()->putJson(sprintf('/api/blog/comments/%d', $comment->getKey()), [
            'comment' => $this->faker()->realText(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete comments.
     */
    public function testCanDeleteComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->deleteJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete comments.
     */
    public function testCannotDeleteComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/comments/%d', $comment->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to approve comments.
     */
    public function testCanApproveComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withRoles(['manage_comments'])->putJson(route('api.comments.update', ['comment' => $comment]), [
            'status' => 'approved',
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to approve comments.
     */
    public function testCannotApproveComment(): void
    {
        $comment = Comment::factory()->for(Article::factory()->hasPostWithUser())->hasPostWithUser()->create();

        $response = $this->withNoRoles()->putJson(route('api.comments.update', ['comment' => $comment]), [
            'status' => 'approved',
        ]);

        $response->assertForbidden();
    }
}
