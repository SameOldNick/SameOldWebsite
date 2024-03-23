<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Arr;

trait WithRoles
{
    use CreatesUser;
    use InteractsWithJWT;

    /**
     * Gets all possible roles.
     *
     * @return array
     */
    protected function possibleRoles(): array
    {
        return Arr::map(config('roles.roles'), fn ($role) => $role['id']);
    }

    /**
     * Attaches user with roles for making HTTP requests.
     *
     * @param array $roles
     * @return static
     */
    protected function withRoles(array $roles): static
    {
        $user = $this->createUser($roles);

        return $this->actingAs($user);
    }
}
