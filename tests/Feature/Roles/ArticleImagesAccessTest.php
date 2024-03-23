<?php

namespace Tests\Feature\Http\Controllers\Api;

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
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Tests user is authorized to get article images.
     *
     * @return void
     */
    public function testCanGetArticleImages(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles(['write_posts'])->getJson(sprintf('/api/blog/articles/%d/images', $article->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get article images.
     *
     * @return void
     */
    public function testCannotGetArticleImages(): void
    {
        $article = Article::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/blog/articles/%d/images', $article->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to attach article image.
     *
     * @return void
     */
    public function testCanAttachArticleImage(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $image = Image::factory()->fakedImage()->create();

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to attach article image.
     *
     * @return void
     */
    public function testCannotAttachArticleImage(): void
    {
        $article = Article::factory()->has(Revision::factory())->create();
        $image = Image::factory()->fakedImage()->create();

        $response = $this->withRoles([])->postJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to detach article image.
     *
     * @return void
     */
    public function testCanDetachArticleImage(): void
    {
        $image = Image::factory()->fakedImage()->create();

        $article = Article::factory()->create();
        $article->images()->attach($image);

        $response = $this->withRoles(['write_posts'])->deleteJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to detach article image.
     *
     * @return void
     */
    public function testCannotDetachArticleImage(): void
    {
        $image = Image::factory()->fakedImage()->create();

        $article = Article::factory()->create();
        $article->images()->attach($image);

        $response = $this->withRoles([])->deleteJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to change article main image.
     *
     * @return void
     */
    public function testCanChangeArticleMainImage(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);

        $response = $this->withRoles(['write_posts'])->postJson(sprintf('/api/blog/articles/%d/images/%s/main-image', $article->getKey(), $image->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to change article main image.
     *
     * @return void
     */
    public function testCannotChangeArticleMainImage(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);

        $response = $this->withRoles([])->postJson(sprintf('/api/blog/articles/%d/images/%s/main-image', $article->getKey(), $image->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to remove article main image.
     *
     * @return void
     */
    public function testCanRemoveArticleMainImage(): void
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
     *
     * @return void
     */
    public function testCannotRemoveArticleMainImage(): void
    {
        $article = Article::factory()->create();
        $image = Image::factory()->fakedImage()->create();

        $article->images()->attach($image);
        $article->mainImage()->associate($image);

        $response = $this->withRoles([])->deleteJson(sprintf('/api/blog/articles/%d/main-image', $article->getKey()));

        $response->assertForbidden();
    }
}
