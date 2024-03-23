<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Revision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ArticleRevisionsAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Tests user is authorized to get article revisions.
     *
     * @return void
     */
    public function testCanGetArticleRevisions(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article revisions.
     *
     * @return void
     */
    public function testCannotGetArticleRevisions(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get article revision.
     *
     * @return void
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
     *
     * @return void
     */
    public function testCannotGetArticleRevision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withRoles([])->getJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create article revisions.
     *
     * @return void
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
     *
     * @return void
     */
    public function testCannotCreateArticleRevision(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles([])->postJson(sprintf('/api/blog/articles/%d/revisions', $article->getKey()), [
            'content' => $this->faker()->paragraphs(4, true),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete article revisions.
     *
     * @return void
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
     *
     * @return void
     */
    public function testCannotDeleteArticleRevision(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $revision = $article->revisions()->first();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/blog/articles/%d/revisions/%s', $article->getKey(), $revision->getKey()));

        $response->assertForbidden();
    }
}
