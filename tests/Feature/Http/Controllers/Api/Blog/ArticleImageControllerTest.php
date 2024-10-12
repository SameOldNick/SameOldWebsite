<?php

namespace Tests\Feature\Http\Controllers\Api\Blog;

use App\Models\Article;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\InteractsWithJWT;
use Tests\TestCase;

class ArticleImageControllerTest extends TestCase
{
    use CreatesUser;
    use InteractsWithJWT;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests article images are fetched.
     */
    public function test_get_all_article_images(): void
    {
        Storage::fake();

        $article = Article::factory()
            ->recycle($this->admin)
            ->createPostWithRegisteredPerson()
            ->withRevision(1)
            ->published()
            ->has(Image::factory(5)->fakedImage(user: $this->admin))
            ->create();

        $response = $this->actingAs($this->admin)->getJson(sprintf('/api/blog/articles/%d/images', $article->getKey()));

        $response
            ->assertSuccessful()
            ->assertJsonCount(5)
            ->assertJsonStructure([
                '*' => [
                    'uuid',
                    'description',
                    'file' => [
                        'id',
                        'name',
                        'meta' => [
                            'size',
                            'last_modified',
                            'mime_type',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Tests a image is attached to article.
     */
    public function test_attach_article_image(): void
    {
        Storage::fake();

        $article = Article::factory()
            ->recycle($this->admin)
            ->createPostWithRegisteredPerson()
            ->withRevision(1)
            ->published()
            ->create();

        $image = Image::factory()->fakedImage(user: $this->admin)->create();

        $response = $this->actingAs($this->admin)->postJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJson([
                ['uuid' => $image->getKey()],
            ]);
    }

    /**
     * Tests a image is attached to article.
     */
    public function test_detach_article_image(): void
    {
        Storage::fake();

        $articleFactory = Article::factory(1)
            ->recycle($this->admin)
            ->createPostWithRegisteredPerson()
            ->withRevision(1)
            ->published();

        $image = Image::factory()->fakedImage(user: $this->admin)->has($articleFactory)->create();

        $article = $image->articles[0];

        $response = $this->actingAs($this->admin)->deleteJson(sprintf('/api/blog/articles/%d/images/%s', $article->getKey(), $image->getKey()));

        $response
            ->assertSuccessful()
            ->assertExactJson([]);

        $this->assertEmpty($article->refresh()->images);
        $this->assertEmpty($image->refresh()->articles);
    }

    /**
     * Tests a image is set as the main image.
     */
    public function test_set_main_image(): void
    {
        Storage::fake();

        $article = Article::factory()
            ->recycle($this->admin)
            ->createPostWithRegisteredPerson()
            ->withRevision(1)
            ->published()
            ->has(Image::factory()->fakedImage(user: $this->admin))
            ->create();

        $image = $article->images->random();

        $response = $this->actingAs($this->admin)->postJson(sprintf('/api/blog/articles/%d/images/%s/main-image', $article->getKey(), $image->getKey()));

        $response
            ->assertSuccessful()
            ->assertJson([
                'main_image' => [
                    'uuid' => $image->getKey(),
                ],
            ]);

        $this->assertTrue($article->refresh()->mainImage->is($image));
    }

    /**
     * Tests a image is unset from the main image.
     */
    public function test_unset_main_image(): void
    {
        Storage::fake();

        $article = Article::factory()
            ->recycle($this->admin)
            ->createPostWithRegisteredPerson()
            ->withRevision(1)
            ->published()
            ->has(Image::factory()->fakedImage(user: $this->admin))
            ->create();

        $image = $article->images->random();

        $article->mainImage()->associate($image);

        $response = $this->actingAs($this->admin)->deleteJson(sprintf('/api/blog/articles/%d/main-image', $article->getKey()));

        $response
            ->assertSuccessful()
            ->assertJson([
                'main_image' => null,
            ]);

        $this->assertNull($article->refresh()->mainImage);
    }
}
