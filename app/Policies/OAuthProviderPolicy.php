<?php

namespace App\Policies;

use App\Models\OAuthProvider;
use App\Models\User;

class OAuthProviderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OAuthProvider $provider): bool
    {
        return $user->is($provider->user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OAuthProvider $provider): bool
    {
        return $user->is($provider->user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OAuthProvider $provider): bool
    {
        return $user->is($provider->user) &&
            // Allow deletion if the user has a password or more than one OAuth provider
            ($user->oauthProviders->count() > 1 || ! is_null($user->password));
    }
}
