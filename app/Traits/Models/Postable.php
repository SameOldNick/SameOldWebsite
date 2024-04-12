<?php

namespace App\Traits\Models;

use App\Models\Post;
use App\Models\User;

/**
 * @property-read Post $post
 */
trait Postable
{
    /**
     * Creates Postable with Post model
     *
     * @param callable $callback
     * @param User|null $user User to associate with Post. If null, current user is used. (default: null)
     * @return static
     */
    public static function createWithPost(callable $callback, User $user = null)
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwned($query, User $user = null)
    {
        $user = $user ?? request()->user();

        return ! is_null($user) ? $query->whereRelation('post', 'user_id', '=', $user->getKey()) : $query;
    }
}
