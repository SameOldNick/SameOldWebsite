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
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Test accessing users when authorized.
     *
     * @return void
     */
    public function testCanGetUsers(): void
    {
        User::factory(5)->create();

        $response = $this->withRoles(['manage_users'])->getJson('/api/users');

        $response->assertSuccessful();
    }

    /**
     * Test accessing users when unauthorized.
     *
     * @return void
     */
    public function testCannotGetUsers(): void
    {
        User::factory(5)->create();

        $response = $this->withRoles([])->getJson('/api/users');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific user when authorized.
     *
     * @return void
     */
    public function testCanGetUser(): void
    {
        $user = User::factory()->create();

        $response = $this->withRoles(['manage_users'])->getJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific user when unauthorized.
     *
     * @return void
     */
    public function testCannotGetUser(): void
    {
        $user = User::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new user when authorized.
     *
     * @return void
     */
    public function testCanCreateUser(): void
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
     *
     * @return void
     */
    public function testCannotCreateUser(): void
    {
        $email = $this->faker()->unique()->email();
        $password = Password::default()->generate();

        $response = $this->withRoles([])->postJson('/api/users', [
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
     *
     * @return void
     */
    public function testCanUpdateUser(): void
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
     *
     * @return void
     */
    public function testCannotUpdateUser(): void
    {
        $user = User::factory()->create();
        $email = $this->faker()->unique()->email();

        $response = $this->withRoles([])->putJson(sprintf('/api/users/%d', $user->getKey()), [
            'name' => $this->faker->name(),
            'email' => $email,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing(User::class, ['email' => $email]);
    }

    /**
     * Test deleting a user when authorized.
     *
     * @return void
     */
    public function testCanDeleteUser(): void
    {
        $user = User::factory()->create();

        $response = $this->withRoles(['manage_users'])->deleteJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertSuccessful();
        $this->assertTrue($user->refresh()->trashed());
    }

    /**
     * Test deleting a user when unauthorized.
     *
     * @return void
     */
    public function testCannotDeleteUser(): void
    {
        $user = User::factory()->create();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/users/%d', $user->getKey()));

        $response->assertForbidden();
        $this->assertFalse($user->refresh()->trashed());
    }
}
