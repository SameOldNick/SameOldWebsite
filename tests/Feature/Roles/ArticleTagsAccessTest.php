<?php

namespace Tests\Feature\Roles;

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
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get article tags.
     */
    public function test_can_get_article_tags(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article tags.
     */
    public function test_cannot_get_article_tags(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to attach article tag.
     */
    public function test_can_attach_article_tag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(5),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to attach article tag.
     */
    public function test_cannot_attach_article_tag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withNoRoles()->postJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(5),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to detach article tag.
     */
    public function test_can_detach_article_tag(): void
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
     */
    public function test_cannot_detach_article_tag(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $tag = Tag::factory()->create();

        $article->tags()->attach($tag);

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => [$tag->tag],
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to sync article tags.
     */
    public function test_can_sync_article_tags(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withRoles(['write_posts'])->putJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to sync article tags.
     */
    public function test_cannot_sync_article_tags(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();

        $response = $this->withNoRoles()->putJson(sprintf('/api/blog/articles/%d/tags', $article->getKey()), [
            'tags' => $this->faker()->words(),
        ]);

        $response->assertForbidden();
    }
}
