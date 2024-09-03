<?php

namespace App\Traits\Models;

use App\Exceptions\NoUserAvailableException;
use App\Models\{Post, Person, User};
use InvalidArgumentException;

/**
 * @property-read Post $post
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> withPersonDetails(array $params)
 * @method static \Illuminate\Database\Eloquent\Builder<static> withPersonDetails(string $field, string $value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> findPersonDetails(array $params)
 * @method static \Illuminate\Database\Eloquent\Builder<static> findPersonDetails(string $field, string $value)
 *
 */
trait Postable
{
    /**
     * Indicates if delete should be cascaded to post.
     */
    protected bool $cascadeToPost = true;

    /**
     * Creates a Postable with a Post model using provided callbacks.
     *
     * @param  callable(static $postable): void  $callback  The callback to configure the Postable instance.
     * @param  callable(Post $post): void    $postCallback  The callback to create and configure the Post instance.
     * @return static
     */
    public static function createWithPost(callable $callback, callable $postCallback)
    {
        return tap(new static, function (self $postable) use ($callback, $postCallback) {
            // Execute the callback to configure the Postable instance
            $callback($postable);

            // Save the Postable instance
            $postable->save();

            // Create Post instance
            $post = new Post();

            // Execute the callback to configure the Post instance
            $postCallback($post, $postable);

            // Associate the Postable model with the Post
            $post->postable()->associate($postable);

            // Save the Post model
            $post->save();
        });
    }

    /**
     * Creates a Postable with a Post model and associates it with a registered User.
     *
     * @param  callable(static $postable): void  $callback  The callback to configure the Postable instance.
     * @param  User|null  $user  The User to associate with the Post. If null, the current user is used. (default: null)
     * @return static
     * @throws NoUserAvailableException  If no User is provided and no current user is available.
     */
    public static function createWithUser(callable $callback, ?User $user = null)
    {
        return static::createWithPost(
            function (self $postable) use ($callback) {
                $callback($postable);
            },
            function (Post $post) use ($user) {
                // Determine the User to associate with

                /**
                 * @var ?User
                 */
                $userToAssociate = $user ?? request()->user();

                // Throw a custom exception if no User is available
                if (!$userToAssociate) {
                    throw new NoUserAvailableException();
                }

                // Associate the Post with the Person model linked to the User
                $person = Person::registered($userToAssociate);

                // Associate the Person model with the Post
                $post->person()->associate($person);
            }
        );
    }

    /**
     * Creates a Postable with a Post model and associates it with a guest's name and email.
     *
     * @param  callable(static $postable): void  $callback  The callback to configure the Postable instance.
     * @param  string  $name  The guest's name.
     * @param  string  $email  The guest's email.
     * @return static
     */
    public static function createWithGuest(callable $callback, string $name, string $email)
    {
        return static::createWithPost(
            function (self $postable) use ($callback) {
                // Execute the callback to configure the Postable instance
                $callback($postable);
            },
            function (Post $post) use ($name, $email) {
                // Get a Person model with the guest's name and email
                $person = Person::guest($name, $email);

                // Associate the Person model with the Post
                $post->person()->associate($person);
            }
        );
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
     * Scope a query to include posts based on person-related details.
     *
     * This scope filters the query based on the provided person details.
     * It supports filtering by 'email', 'name', or 'user_id'. The method
     * can accept either a single field-value pair or an associative array
     * of field-value pairs.
     *
     * Example usage:
     *
     * ```php
     * // Single field and value
     * $comment = Postable::withPersonDetails('email', $email)->first();
     *
     * // Multiple fields and values
     * $comment = Postable::withPersonDetails([
     *     'email' => $email,
     *     'user_id' => $userId,
     * ])->first();
     * ```
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  ...$params  The field name and value, or an associative array of field names and values.
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \InvalidArgumentException  If an unsupported field is provided.
     */
    public function scopeWithPersonDetails($query, ...$params)
    {
        $details = is_array($params[0]) ? $params[0] : [$params[0] => $params[1]];

        return $query->whereHas('post', function ($query) use ($details) {
            foreach ($details as $field => $value) {
                match ($field) {
                    'email' => $query->withEmail($value),
                    'name' => $query->withName($value),
                    'user' => $query->owned($value->getKey()),
                    'user_id' => $query->owned($value),
                    default => throw new InvalidArgumentException("Unsupported field: $field"),
                };
            }
        });
    }

    /**
     * Scope a query to include posts based on person-related details.
     *
     * This scope filters the query based on the provided person details.
     * It supports finding by 'email', 'name', or 'user_id'. The method
     * can accept either a single field-value pair or an associative array
     * of field-value pairs.
     *
     * Example usage:
     *
     * ```php
     * // Single field and value
     * $comment = Postable::findPersonDetails('email', $email)->first();
     *
     * // Multiple fields and values
     * $comment = Postable::findPersonDetails([
     *     'email' => $email,
     *     'user_id' => $userId,
     * ])->first();
     * ```
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  ...$params  The field name and value, or an associative array of field names and values.
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \InvalidArgumentException  If an unsupported field is provided.
     */
    public function scopeFindPersonDetails($query, ...$params)
    {
        $details = is_array($params[0]) ? $params[0] : [$params[0] => $params[1]];

        return $query->whereHas('post', function ($query) use ($details) {
            foreach ($details as $field => $value) {
                match ($field) {
                    'email' => $query->findEmail($value),
                    'name' => $query->findName($value),
                    default => throw new InvalidArgumentException("Unsupported field: $field"),
                };
            }
        });
    }
}
