<?php

namespace Tests\Feature\Roles;

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ImagesAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user with role is authorized to get skills.
     */
    #[Test]
    public function can_get_all_images_with_role(): void
    {
        [$user1, $user2] = User::factory(2)->create();

        $user1Images = Image::factory(5)->fakedImage(user: $user1)->create();
        $user2Images = Image::factory(5)->fakedImage(user: $user2)->create();

        $response = $this->withRoles(['manage_images'])->getJson(route('api.images.index'));

        $response
            ->assertSuccessful()
            ->assertJson([...$user1Images->toArray(), ...$user2Images->toArray()], true);
    }

    /**
     * Tests user without is unauthorized to get skills.
     */
    #[Test]
    public function can_get_all_images_without_role(): void
    {
        [$user1, $user2] = User::factory(2)->create();

        $user1Images = Image::factory(5)->fakedImage(user: $user1)->create();
        $user2Images = Image::factory(5)->fakedImage(user: $user2)->create();

        $response = $this->actingAs($user2)->getJson(route('api.images.index'));

        $response
            ->assertSuccessful()
            ->assertJson($user2Images->toArray(), true);
    }

    /**
     * Tests user with role is authorized to get image.
     */
    #[Test]
    public function can_get_image_with_role(): void
    {
        [$user1, $user2] = User::factory(2)->create();

        $images = [
            ...Image::factory(5)->fakedImage(user: $user1)->create(),
            ...Image::factory(5)->fakedImage(user: $user2)->create(),
        ];

        $image = Arr::random($images);

        $response = $this->withRoles(['manage_images'])->getJson(route('api.images.show', ['image' => $image]));

        $response
            ->assertSuccessful()
            ->assertJson($image->toArray());
    }

    /**
     * Tests user with role is authorized to get image.
     */
    #[Test]
    public function can_get_image_without_role(): void
    {
        [$user1, $user2] = User::factory(2)->create();

        $user1Images = Image::factory(5)->fakedImage(user: $user1)->create();
        $user2Images = Image::factory(5)->fakedImage(user: $user2)->create();

        $image = $user2Images->random();

        $response = $this->actingAs($user2)->getJson(route('api.images.show', ['image' => $image]));

        $response
            ->assertSuccessful()
            ->assertJson($image->toArray());
    }

    /**
     * Tests user with role is authorized to get image.
     */
    #[Test]
    public function cannot_get_image_without_role(): void
    {
        [$user1, $user2] = User::factory(2)->create();

        $user1Images = Image::factory(5)->fakedImage(user: $user1)->create();
        $user2Images = Image::factory(5)->fakedImage(user: $user2)->create();

        $image = $user1Images->random();

        $response = $this->actingAs($user2)->getJson(route('api.images.show', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * Tests user with role is authorized to post image.
     */
    #[Test]
    public function can_post_image_with_role(): void
    {
        Storage::fake();

        $name = sprintf('%s.jpg', $this->faker()->uuid);
        $uploadedFile = UploadedFile::fake()->image($name);

        $response = $this->withRoles(['manage_images'])->postJson(route('api.images.store'), [
            'image' => $uploadedFile,
            'description' => $this->faker->text,
        ]);

        $response->assertSuccessful();

        Storage::assertExists($uploadedFile->hashName('images'));
    }

    /**
     * Tests user with role is authorized to post image and assign user.
     */
    #[Test]
    public function can_post_image_assign_user_with_role(): void
    {
        Storage::fake();

        $user = User::factory()->create();

        $name = sprintf('%s.jpg', $this->faker()->uuid);
        $uploadedFile = UploadedFile::fake()->image($name);

        $data = [
            'image' => $uploadedFile,
            'description' => $this->faker->text,
            'user' => $user->getKey()
        ];

        $response = $this->withRoles(['manage_images'])->postJson(route('api.images.store'), $data);

        $response->assertSuccessful();

        Storage::assertExists($uploadedFile->hashName('images'));

        $this->assertNotNull($image = Image::firstWhere('description', $data['description']));
        $this->assertEquals($user->getKey(), $image->file->user->getKey());
    }

    /**
     * Tests user without role is authorized to post image but not assign user.
     */
    #[Test]
    public function can_post_image_cannot_assign_user_without_role(): void
    {
        Storage::fake();

        $user = User::factory()->create();

        $name = sprintf('%s.jpg', $this->faker()->uuid);
        $uploadedFile = UploadedFile::fake()->image($name);

        $data = [
            'image' => $uploadedFile,
            'description' => $this->faker->text,
            'user' => $user->getKey()
        ];

        $response = $this->actingAs($this->user)->postJson(route('api.images.store'), $data);

        $response->assertSuccessful();

        Storage::assertExists($uploadedFile->hashName('images'));

        $this->assertNotNull($image = Image::firstWhere('description', $data['description']));
        $this->assertNotEquals($user->getKey(), $image->file->user->getKey());
        $this->assertEquals($this->user->getKey(), $image->file->user->getKey());
    }

    /**
     * Tests user without role is authorized to post image.
     */
    #[Test]
    public function can_post_image_without_role(): void
    {
        Storage::fake();

        $name = sprintf('%s.jpg', $this->faker()->uuid);
        $uploadedFile = UploadedFile::fake()->image($name);

        $response = $this->actingAs($this->user)->postJson(route('api.images.store'), [
            'image' => $uploadedFile,
            'description' => $this->faker->text,
        ]);

        $response->assertSuccessful();

        Storage::assertExists($uploadedFile->hashName('images'));
    }

    /**
     * Tests guest user is not authorized to post image.
     */
    #[Test]
    public function cannot_post_image_guest(): void
    {
        Storage::fake();

        $name = sprintf('%s.jpg', $this->faker()->uuid);
        $uploadedFile = UploadedFile::fake()->image($name);

        $response = $this->postJson(route('api.images.store'), [
            'image' => $uploadedFile,
            'description' => $this->faker->text,
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user with role change image user
     */
    #[Test]
    public function user_with_role_can_change_image_user()
    {
        $user = User::factory()->create();
        $image = Image::factory()->fakedImage(user: $this->user)->create();

        $response = $this->withRoles(['manage_images'])->putJson(route('api.images.update', ['image' => $image]), [
            'user' => $user->getKey(),
        ]);

        $response->assertSuccessful();

        $this->assertEquals($user->getKey(), $image->refresh()->file->user->getKey());
    }

    /**
     * Tests user with role change image user
     */
    #[Test]
    public function user_without_role_cannot_change_image_user()
    {
        $user = User::factory()->create();
        $image = Image::factory()->fakedImage(user: $this->user)->create();

        $response = $this->actingAs($this->user)->putJson(route('api.images.update', ['image' => $image]), [
            'user' => $user->getKey(),
        ]);

        $response->assertSuccessful();

        $this->assertNotEquals($user->getKey(), $image->refresh()->file->user->getKey());
    }

    /**
     * Tests user with role can change image description
     */
    #[Test]
    public function user_with_role_can_change_image_description()
    {
        $user = User::factory()->create();
        $image = Image::factory()->fakedImage(user: $user)->create();

        $data = [
            'description' => $this->faker->sentence,
        ];

        $response = $this->withRoles(['manage_images'])->putJson(route('api.images.update', ['image' => $image]), $data);

        $response->assertSuccessful();

        $this->assertEquals($data['description'], $image->refresh()->description);
    }

    /**
     * Tests user without role can change image description
     */
    #[Test]
    public function user_without_role_can_change_image_description()
    {
        $image = Image::factory()->fakedImage(user: $this->user)->create();

        $data = [
            'description' => $this->faker->sentence,
        ];

        $response = $this->actingAs($this->user)->putJson(route('api.images.update', ['image' => $image]), $data);

        $response->assertSuccessful();

        $this->assertEquals($data['description'], $image->refresh()->description);
    }

    /**
     * Tests user without role cannot change other image description
     */
    #[Test]
    public function user_without_role_cannot_change_other_image_description()
    {
        $user = User::factory()->create();
        $image = Image::factory()->fakedImage(user: $user)->create();

        $data = [
            'description' => $this->faker->sentence,
        ];

        $response = $this->actingAs($this->user)->putJson(route('api.images.update', ['image' => $image]), $data);

        $response->assertForbidden();

        $this->assertNotEquals($data['description'], $image->refresh()->description);
    }

    /**
     * Tests user with role can delete image
     */
    #[Test]
    public function user_with_role_can_delete_image()
    {
        $user = User::factory()->create();
        $image = Image::factory()->fakedImage(user: $user)->create();

        $response = $this->withRoles(['manage_images'])->deleteJson(route('api.images.destroy', ['image' => $image]));

        $response->assertSuccessful();

        $this->assertModelMissing($image);
    }

    /**
     * Tests user without role can delete image
     */
    #[Test]
    public function user_without_role_can_delete_image()
    {
        $image = Image::factory()->fakedImage(user: $this->user)->create();

        $response = $this->actingAs($this->user)->deleteJson(route('api.images.destroy', ['image' => $image]));

        $response->assertSuccessful();

        $this->assertModelMissing($image);
    }

    /**
     * Tests user without role cannot delete other image
     */
    #[Test]
    public function user_without_role_cannot_delete_other_image()
    {
        $user = User::factory()->create();
        $image = Image::factory()->fakedImage(user: $user)->create();

        $response = $this->actingAs($this->user)->deleteJson(route('api.images.destroy', ['image' => $image]));

        $response->assertForbidden();

        $this->assertModelExists($image);
    }
}
