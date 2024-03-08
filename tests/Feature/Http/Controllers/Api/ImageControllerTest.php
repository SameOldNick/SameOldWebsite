<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\InteractsWithJWT;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;
    use InteractsWithJWT;
    use WithFaker;

    public function test_get_all_images()
    {
        Image::factory(5)->fakedImage()->create();

        $response = $this->actingAs($this->admin)->getJson('/api/images');

        $response
            ->assertSuccessful()
            ->assertJsonIsArray()
            ->assertJsonStructure([
                '*' => [
                    'uuid',
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
                ],
            ]);
    }

    /**
     * Tests an image is retrieved.
     *
     * @return void
     */
    public function test_retrieve_image()
    {
        Storage::fake();

        $image = Image::factory()->fakedImage(user: $this->admin)->create();

        $response = $this->actingAs($this->admin)->getJson(sprintf('/api/images/%s', $image->getKey()));

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'uuid',
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
            ])
            ->assertJson(['uuid' => $image->getKey()]);
    }

    /**
     * Tests a valid image is uploaded.
     */
    public function test_upload_image(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->image(sprintf('%s.jpg', $this->faker->sha1));

        $response = $this->actingAs($this->admin)->postJson('/api/images', [
            'image' => $file,
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'uuid',
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

        $image = Image::all()->last();

        $this->assertEquals($response->json('uuid'), $image->getKey());
        $this->assertTrue($image->file->user->is($this->admin));

        Storage::assertExists(sprintf('images/%s', $file->hashName()));
    }

    /**
     * Tests an image is deleted.
     *
     * @return void
     */
    public function test_delete_image()
    {
        Storage::fake();

        $image = Image::factory()->fakedImage(user: $this->admin)->create();

        $response = $this->actingAs($this->admin)->deleteJson(sprintf('/api/images/%s', $image->getKey()));

        $response
            ->assertSuccessful()
            ->assertJsonStructure(['success']);

        $this->assertEmpty(Image::all());
    }
}
