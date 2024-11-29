<?php

namespace Tests\Feature\Roles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ArticlesAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get articles.
     */
    public function test_can_get_articles(): void
    {
        $response = $this->withRoles(['write_posts'])->getJson('/api/blog/articles');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get articles.
     */
    public function test_cannot_get_articles(): void
    {
        $response = $this->withNoRoles()->getJson('/api/blog/articles');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get article.
     */
    public function test_can_get_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article.
     */
    public function test_cannot_get_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create articles.
     */
    public function test_can_create_article(): void
    {
        $title = Str::headline($this->faker->unique()->realText(25));

        $response = $this->withRoles(['write_posts'])->postJson('/api/blog/articles', [
            'title' => $title,
            'slug' => Str::slug($title),
            'revision' => [
                'content' => $this->faker()->paragraphs(4, true),
            ],
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to create articles.
     */
    public function test_cannot_create_article(): void
    {
        $response = $this->withNoRoles()->postJson('/api/blog/articles', [
            'icon' => $this->faker->iconName(),
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update articles.
     */
    public function test_can_update_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->putJson(sprintf('/api/blog/articles/%d', $article->getKey()), [
            'icon' => $this->faker->iconName(),
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to update articles.
     */
    public function test_cannot_update_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->putJson(sprintf('/api/blog/articles/%d', $article->getKey()), [
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete articles.
     */
    public function test_can_delete_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete articles.
     */
    public function test_cannot_delete_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/articles/%d', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to restore articles.
     */
    public function test_can_restore_article(): void
    {
        $article = Article::factory()->deleted()->create();

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/restore/%d', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to restore articles.
     */
    public function test_cannot_restore_article(): void
    {
        $article = Article::factory()->deleted()->create();

        $response = $this->withNoRoles()->postJson(sprintf('/api/blog/articles/restore/%d', $article->getKey()));

        $response->assertForbidden();
    }
}
