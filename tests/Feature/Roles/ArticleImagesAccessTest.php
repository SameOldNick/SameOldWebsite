<?php

namespace Tests\Feature\Roles;

use App\Models\Article;
use App\Models\Image;
use App\Models\Revision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ArticleImagesAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get article images.
     */
    public function test_can_get_article_images(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/images', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article images.
     */
    public function test_cannot_get_article_images(): void
    {
        $article = Article::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/blog/articles/%d/images', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to attach article image.
     */
    public function test_can_attach_article_image(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $image = Image::factory()->fakedImage()->create();

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to attach article image.
     */
    public function test_cannot_attach_article_image(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $image = Image::factory()->fakedImage()->create();

        $response = $this->withNoRoles()->postJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to detach article image.
     */
    public function test_can_detach_article_image(): void
    {
        $image = Image::factory()->fakedImage()->create();

        $article = Article::factory()->create();
        $article->images()->attach($image);

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to detach article image.
     */
    public function test_cannot_detach_article_image(): void
    {
        $image = Image::factory()->fakedImage()->create();

        $article = Article::factory()->create();
        $article->images()->attach($image);

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to change article main image.
     */
    public function test_can_change_article_main_image(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/images/%s/main-image', $article->getKey(), $image->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to change article main image.
     */
    public function test_cannot_change_article_main_image(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);

        $response = $this->withNoRoles()->postJson(sprintf('/api/blog/articles/%d/images/%s/main-image', $article->getKey(), $image->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to remove article main image.
     */
    public function test_can_remove_article_main_image(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);
        $article->mainImage()->associate($image);

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d/main-image', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to remove article main image.
     */
    public function test_cannot_remove_article_main_image(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);
        $article->mainImage()->associate($image);

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/blog/articles/%d/main-image', $article->getKey()));

        $response->assertForbidden();
    }
}
