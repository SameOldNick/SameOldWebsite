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
    public function testCanGetArticleRevisions(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article revisions.
     */
    public function testCannotGetArticleRevisions(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get article revision.
     */
    public function testCanGetArticleRevision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article revision.
     */
    public function testCannotGetArticleRevision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create article revisions.
     */
    public function testCanCreateArticleRevision(): void
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
    public function testCannotCreateArticleRevision(): void
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
    public function testCanDeleteArticleRevision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete article revisions.
     */
    public function testCannotDeleteArticleRevision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertForbidden();
    }
}
