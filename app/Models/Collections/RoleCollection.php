<?php

namespace App\Models\Collections;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends Collection<int, Role>
 */
class RoleCollection extends Collection
{
    /**
     * Gets the role names
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function names()
    {
        return $this->map(fn (Role $role) => $role->role)->unique();
    }

    /**
     * Checks if has all specified roles
     *
     * @param  list  $roles  Array of role names
     * @return bool
     */
    public function containsAll(array $roles)
    {
        // If no roles, skip checking and return true/false depending on if is expected to have roles.
        if ($this->count() === 0) {
            return empty($roles);
        }

        // Get the role models and extract role names
        $names = $this->names();

        // Check if all specified roles are matched with user roles
        $matchedRoles = $names->intersect($roles);

        // Return true if the number of matched roles equals the total number of specified roles
        return count($roles) === count($matchedRoles);
    }

    /**
     * Checks if has any specified roles.
     *
     * @param  list  $roles  Array of role names to check.
     * @return bool True if has any specified roles, false otherwise.
     */
    public function containsAny(array $roles): bool
    {
        // If has no roles, skip checking and return true/false depending on if is expected to have roles.
        if ($this->count() === 0) {
            return empty($roles);
        }

        // Get the role models and extract role names
        $names = $this->names();

        // Return true if there are any matched roles
        return $names->intersect($roles)->isNotEmpty();
    }
}
