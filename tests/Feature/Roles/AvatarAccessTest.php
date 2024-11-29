<?php

namespace Tests\Feature\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class AvatarAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to upload avatar.
     */
    public function test_can_upload_avatar(): void
    {
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withRoles(['change_avatar'])->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to upload avatar.
     */
    public function test_cannot_upload_avatar(): void
    {
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withNoRoles()->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete avatar.
     */
    public function test_can_delete_avatar(): void
    {
        $response = $this->withRoles(['change_avatar'])->deleteJson('/api/user/avatar');

        $response->assertNotFound();
    }

    /**
     * Tests user is unauthorized to delete avatar.
     */
    public function test_cannot_delete_avatar(): void
    {
        $response = $this->withNoRoles()->deleteJson('/api/user/avatar');

        $response->assertForbidden();
    }
}
