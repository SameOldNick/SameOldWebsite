<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $jwt_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Carbon $expires_at
 * @property-read User $user
 */
class RefreshToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['jwt_id', 'expires_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Gets the user who made this refresh token is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
