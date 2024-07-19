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

    /**
     * Attaches user with all roles except those specified.
     *
     * @param array $roles Roles to exclude
     */
    protected function withoutRoles(array $roles = []) {
        return $this->withRoles(array_diff($this->possibleRoles(), $roles));
    }

    /**
     * Attaches user with no roles.
     */
    protected function noRoles() {
        return $this->withRoles([]);
    }
}
