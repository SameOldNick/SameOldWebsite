<?php

namespace Tests\Feature\Http\Controllers\Api\Blog;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\InteractsWithJWT;
use Tests\TestCase;

class ArticleImageControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;
    use InteractsWithJWT;
    use WithFaker;

    /**
     * Tests a valid image is uploaded.
     */
    public function test_upload_image(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->image(sprintf('%s.jpg', $this->faker->sha1));

        $article =
            Article::factory()
                ->recycle($this->admin)
                ->hasPostWithUser()
                ->withRevision(1)
                ->published()
                ->create();

        $response = $this->actingAs($this->admin)->postJson(sprintf('/api/blog/articles/%d/images', $article->getKey()), [
            'image' => $file,
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'id',
                'description',
                'file' => [
                    'id',
                    'name',
                    'url',
                    'meta' => [
                        'size',
                        'last_modified',
                        'mime_type',
                    ],
                ],
            ]);

        Storage::assertExists(sprintf('images/%s', $file->hashName()));
    }

    /**
     * Tests a non-image file is uploaded.
     */
    public function test_upload_non_image(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->image(sprintf('%s.php', $this->faker->sha1));

        $article =
            Article::factory()
                ->recycle($this->admin)
                ->hasPostWithUser()
                ->withRevision(1)
                ->published()
                ->create();

        $response = $this->actingAs($this->admin)->postJson(sprintf('/api/blog/articles/%d/images', $article->getKey()), [
            'image' => $file,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        Storage::assertMissing(sprintf('images/%s', $file->hashName()));
    }
}
