<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property-read User|null $user
 * @property \DateTimeInterface|null $created_at
 * @property \DateTimeInterface|null $updated_at
 * @property \DateTimeInterface|null $deleted_at
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
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the morphed child of this post
     *
     * @return mixed
     */
    public function postable()
    {
        return $this->morphTo();
    }
}
