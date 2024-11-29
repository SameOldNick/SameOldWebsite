<?php

namespace Tests\Feature\Roles;

use App\Models\Article;
use App\Models\Revision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ArticleRevisionsAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get article revisions.
     */
    public function test_can_get_article_revisions(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article revisions.
     */
    public function test_cannot_get_article_revisions(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get article revision.
     */
    public function test_can_get_article_revision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article revision.
     */
    public function test_cannot_get_article_revision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create article revisions.
     */
    public function test_can_create_article_revision(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()), [
            'content' => $this->faker()->paragraphs(4, true),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to create article revisions.
     */
    public function test_cannot_create_article_revision(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->postJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()), [
            'content' => $this->faker()->paragraphs(4, true),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete article revisions.
     */
    public function test_can_delete_article_revision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete article revisions.
     */
    public function test_cannot_delete_article_revision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertForbidden();
    }
}
