<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
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
    public function view(?User $user, Article $article)
    {
        return ! is_null($article->published_at) && $article->published_at->isPast() ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        return ($article->post->user && $article->post->user->is($user)) || $user->roles->containsAll(['write_posts']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine if image can be attached to article.
     */
    public function attach(User $user, Article $article, Image $image): bool
    {
        return true;
    }

    /**
     * Determine if image can be detached from article.
     */
    public function detach(User $user, Article $article, Image $image): bool
    {
        return true;
    }

    /**
     * Determine if image can be set as main image for article.
     */
    public function mainImage(User $user, Article $article, Image $image): bool
    {
        return true;
    }

    /**
     * Determine if main image can be removed from article.
     */
    public function destroyMainImage(User $user, Article $article): bool
    {
        return true;
    }
}
