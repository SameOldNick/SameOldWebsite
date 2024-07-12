<?php

namespace App\Traits\Models;

use App\Models\Post;
use App\Models\User;

/**
 * @property-read ?Post $post
 */
trait Postable
{
    /**
     * Indicates if delete should be cascaded to post.
     */
    protected bool $cascadeToPost = true;

    /**
     * Creates Postable with Post model
     *
     * @param  User|null  $user  User to associate with Post. If null, current user is used. (default: null)
     * @return static
     */
    public static function createWithPost(callable $callback, ?User $user = null)
    {
        return tap(new static, function (self $postable) use ($callback, $user) {
            $callback($postable);

            $postable->save();

            $postable->post->user()->associate($user ?? request()->user());
            $postable->post->postable()->associate($postable);

            $postable->post->save();
        });
    }

    /**
     * Boots the trait
     *
     * @return void
     */
    public static function bootPostable()
    {
        static::registerModelEvent('restoring', function (self $model) {
            if ($model->getCascadeToPost()) {
                $model->post->restore();
            }
        });

        static::registerModelEvent('deleting', function (self $model) {
            if ($model->getCascadeToPost()) {
                $model->post->delete();
            }
        });
    }

    /**
     * Gets whether changes should be cascaded to post.
     */
    public function getCascadeToPost(): bool
    {
        return $this->cascadeToPost;
    }

    /**
     * Gets the Post this is morphed from.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function post()
    {
        return
            $this->morphOne(Post::class, 'postable')
                ->withTrashed()
                ->withDefault(fn () => new Post);
    }

    /**
     * Scope a query to only include users own posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $user  User model, key, or null. If null, uses current user.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwned($query, $user = null)
    {
        $key = match (true) {
            $user instanceof User => $user->getKey(),
            is_null($user) && ! is_null(request()->user()) => request()->user()->getKey(),
            default => $user
        };

        return ! is_null($key) ? $query->whereHas('post', function ($query) use ($key) {
            $query->where('user_id', $key);
        }) : $query;
    }
}
