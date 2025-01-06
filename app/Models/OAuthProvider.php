<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $provider_name
 * @property string $provider_id
 * @property string $access_token
 * @property ?string $refresh_token
 * @property ?string $avatar_url
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $expires_at
 * @property-read User $user
 */
class OAuthProvider extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_providers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];

    /**
     * The relations to eager load on every query.
     *
     * @var list<string>
     */
    protected $with = [];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array<string>
     */
    protected $visible = [
        'provider_name',
        'avatar_url',
        'created_at',
        'updated_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Gets the user the OAuth provider belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
