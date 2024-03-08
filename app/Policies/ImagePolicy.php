<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ImagePolicy
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
    public function view(User $user, Image $image): bool
    {
        return $image->file->user->is($user) || $user->hasRoles(['admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Image $image): bool
    {
        return ($image->file->user && $image->file->user->is($user)) || $user->hasRoles(['admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Image $image): bool
    {
        return ($image->file->user && $image->file->user->is($user)) || $user->hasRoles(['admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Image $image): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Image $image): bool
    {
        //
    }
}
