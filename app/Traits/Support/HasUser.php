<?php

namespace App\Traits\Support;

use App\Models\User;

trait HasUser
{
    protected ?User $user;

    /**
     * Gets the user.
     *
     * @return User|null
     */
    public function user()
    {
        return $this->getUser();
    }

    /**
     * Gets the user
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user ?? $this->getDefaultUser();
    }

    /**
     * Gets the default user.
     *
     * @return User|null
     */
    protected function getDefaultUser()
    {
        return request()->user();
    }
}
