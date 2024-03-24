<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Revision;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ArticleTagsAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Tests user is authorized to get article tags.
     *
     * @return void
     */
    public function testCanGetArticleTags(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article tags.
     *
     * @return void
     */
    public function testCannotGetArticleTags(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to attach article tag.
     *
     * @return void
     */
    public function testCanAttachArticleTag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(5),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to attach article tag.
     *
     * @return void
     */
    public function testCannotAttachArticleTag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withRoles([])->postJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(5),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to detach article tag.
     *
     * @return void
     */
    public function testCanDetachArticleTag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $tag = Tag::factory()->create();

        $article->tags()->attach($tag);

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => [$tag->tag],
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to detach article tag.
     *
     * @return void
     */
    public function testCannotDetachArticleTag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $tag = Tag::factory()->create();

        $article->tags()->attach($tag);

        $response = $this->withRoles([])->deleteJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => [$tag->tag],
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to sync article tags.
     *
     * @return void
     */
    public function testCanSyncArticleTags(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withRoles(['write_posts'])->putJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to sync article tags.
     *
     * @return void
     */
    public function testCannotSyncArticleTags(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withRoles([])->putJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(),
        ]);

        $response->assertForbidden();
    }
}
