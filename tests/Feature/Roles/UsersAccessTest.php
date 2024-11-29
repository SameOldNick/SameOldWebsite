<?php

namespace Tests\Feature\Roles;

use App\Components\Passwords\Password;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class UsersAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Test accessing users when authorized.
     */
    public function test_can_get_users(): void
    {
        User::factory(5)->create();

        $response = $this->withRoles(['manage_users'])->getJson('/api/users');

        $response->assertSuccessful();
    }

    /**
     * Test accessing users when unauthorized.
     */
    public function test_cannot_get_users(): void
    {
        User::factory(5)->create();

        $response = $this->withNoRoles()->getJson('/api/users');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific user when authorized.
     */
    public function test_can_get_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withRoles(['manage_users'])->getJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific user when unauthorized.
     */
    public function test_cannot_get_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new user when authorized.
     */
    public function test_can_create_user(): void
    {
        $email = $this->faker()->unique()->email();
        $password = Password::default()->generate();

        $response = $this->withRoles(['manage_users'])->postJson('/api/users', [
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas(User::class, ['email' => $email]);
    }

    /**
     * Test creating a new user when unauthorized.
     */
    public function test_cannot_create_user(): void
    {
        $email = $this->faker()->unique()->email();
        $password = Password::default()->generate();

        $response = $this->withNoRoles()->postJson('/api/users', [
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing(User::class, ['email' => $email]);
    }

    /**
     * Test updating an existing user when authorized.
     */
    public function test_can_update_user(): void
    {
        $user = User::factory()->create();
        $email = $this->faker()->unique()->email();

        $response = $this->withRoles(['manage_users'])->putJson(sprintf('/api/users/%d', $user->getKey()), [
            'name' => $this->faker->name(),
            'email' => $email,
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas(User::class, ['email' => $email]);
    }

    /**
     * Test updating an existing user when unauthorized.
     */
    public function test_cannot_update_user(): void
    {
        $user = User::factory()->create();
        $email = $this->faker()->unique()->email();

        $response = $this->withNoRoles()->putJson(sprintf('/api/users/%d', $user->getKey()), [
            'name' => $this->faker->name(),
            'email' => $email,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing(User::class, ['email' => $email]);
    }

    /**
     * Test deleting a user when authorized.
     */
    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withRoles(['manage_users'])->deleteJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertSuccessful();
        $this->assertTrue($user->refresh()->trashed());
    }

    /**
     * Test deleting a user when unauthorized.
     */
    public function test_cannot_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertForbidden();
        $this->assertFalse($user->refresh()->trashed());
    }
}
