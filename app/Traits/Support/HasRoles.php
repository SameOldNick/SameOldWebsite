<?php

namespace App\Traits\Support;

trait HasRoles
{
    /**
     * Checks if user has roles
     *
     * @param  array  $roles  Expected roles
     * @param  bool  $strict  If true, user must have all roles.
     */
    public function hasRoles(array $roles, bool $strict = true): bool
    {
        if (is_null($user = $this->user())) {
            return false;
        }

        return $strict ? $user->roles->containsAll($roles) : $user->roles->containsAny($roles);
    }
}
