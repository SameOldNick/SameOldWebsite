<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property-read Person $person
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 *
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 */
class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'postable_type',
        'postable_id',
        /**
         * Prevents circular serialization (Postable -> Post -> Postable -> Post...)
         */
        'postable',
    ];

    /**
     * Gets the person who made this post.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Gets the morphed child of this post
     */
    public function postable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include posts from user ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId  User ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwned($query, int $userId)
    {
        return $query->whereHas('person', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    /**
     * Scope a query to only include posts from user with name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindName($query, string $name, bool $caseSensitive = false)
    {
        return $query->whereHas('person', function (Builder $query) use ($name, $caseSensitive) {
            $query->whereHas('user', function (Builder $userQuery) use ($name, $caseSensitive) {
                $userQuery->search('name', $name, $caseSensitive);
            })->orWhere(function (Builder $query) use ($name, $caseSensitive) {
                $query->search('name', $name, $caseSensitive);
            });
        });
    }

    /**
     * Scope a query to only include posts from user with name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithName($query, string $name)
    {
        return $query->whereHas('person', function ($query) use ($name) {
            $query->whereHas('user', function ($userQuery) use ($name) {
                $userQuery->where('name', $name);
            })->orWhere('name', $name);
        });
    }

    /**
     * Scope a query to only include posts from user with email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindEmail($query, string $email, bool $caseSensitive = false)
    {
        return $query->whereHas('person', function (Builder $query) use ($email, $caseSensitive) {
            $query->whereHas('user', function (Builder $userQuery) use ($email, $caseSensitive) {
                $userQuery->search('email', $email, $caseSensitive);
            })->orWhere(function (Builder $query) use ($email, $caseSensitive) {
                $query->search('email', $email, $caseSensitive);
            });
        });
    }

    /**
     * Scope a query to only include posts from user with email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithEmail($query, string $email)
    {
        return $query->whereHas('person', function ($query) use ($email) {
            $query->whereHas('user', function ($userQuery) use ($email) {
                $userQuery->where('email', $email);
            })->orWhere('email', $email);
        });
    }
}
