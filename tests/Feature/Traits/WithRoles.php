<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Arr;

trait WithRoles
{
    use CreatesUser;
    use InteractsWithJWT;

    /**
     * Gets all possible roles.
     */
    protected function possibleRoles(): array
    {
        return Arr::map(config('roles.roles'), fn ($role) => $role['id']);
    }

    /**
     * Attaches user with roles for making HTTP requests.
     */
    protected function withRoles(array $roles): static
    {
        $user = $this->createUser($roles);

        return $this->actingAs($user);
    }
}
