<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property-read ?User $user
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
        'user',
    ];

    /**
     * Gets the user who made this post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the morphed child of this post
     */
    public function postable(): MorphTo
    {
        return $this->morphTo();
    }
}
