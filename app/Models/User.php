<?php

namespace App\Models;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Stores\Eloquent\Models\OneTimePasscodeSecret;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $postal_code
 * @property string|null $country_code
 * @property-read string $avatar_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Country|null $country
 * @property-read State|null $state
 * @property-read \App\Models\Collections\RoleCollection $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OAuthProvider> $oauthProviders
 * @property-read \App\Models\Collections\PrivateChannelCollection $privateChannels
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 */
class User extends Authenticatable implements MultiAuthenticatable, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'state_id',
        'address1',
        'address2',
        'city',
        'postal_code',
        'country_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'deleted_at',
        'address1',
        'address2',
        'city',
        'postal_code',
        'state_id',
        'country_code',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = ['roles', 'state', 'country', 'oauthProviders'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['avatar_url'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds()
    {
        return ['uuid'];
    }

    /**
     * Get the entity's notifications.
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Checks if User has all specified roles
     *
     * @param  array  $roles  Array of role names
     */
    public function hasAllRoles(array $roles): bool
    {
        // If user has no roles, skip checking and return true/false depending on if user is expected to have roles.
        if (count($this->roles) === 0) {
            return count($roles) === 0;
        }

        // Get the roles associated with the user and extract role names
        $userRoles = $this->roles->map(fn ($role) => $role->role);

        // Check if all specified roles are matched with user roles
        $matchedRoles = $userRoles->intersect($roles);

        // Return true if the number of matched roles equals the total number of specified roles
        return count($roles) === count($matchedRoles);
    }

    /**
     * Checks if the user has any specified roles.
     *
     * @param  array  $roles  Array of role names to check.
     * @return bool True if the user has any specified roles, false otherwise.
     */
    public function hasAnyRoles(array $roles): bool
    {
        // If user has no roles, skip checking and return true/false depending on if user is expected to have roles.
        if (count($this->roles) === 0) {
            return count($roles) === 0;
        }

        // Get the roles associated with the user and extract role names
        $userRoles = $this->roles->map(fn ($role) => $role->role);

        // Return true if there are any matched roles
        return $userRoles->intersect($roles)->isNotEmpty();
    }

    /**
     * Gets posts created by this user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Gets files uploaded by this user.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Gets comments approved by this user.
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'approved_by');
    }

    /**
     * Gets refresh tokens for this user.
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Gets the private channels for this user.
     */
    public function privateChannels(): MorphMany
    {
        return $this->morphMany(PrivateChannel::class, 'notifiable');
    }

    /**
     * Gets the country for this user
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Gets the state for this user
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Checks if state is to be pulled from related table.
     *
     * @return bool
     */
    public function isStateAssociated()
    {
        return ! is_null($this->country_code) && $this->country->states->count() > 0;
    }

    /**
     * Gets name to publicly display
     *
     * @return string
     */
    public function getDisplayName()
    {
        // TODO: Make this an attribute.
        return $this->name ?? Str::before($this->email, '@');
    }

    /**
     * Gets the avatar URL for the user
     *
     * @return string
     */
    public function getAvatarUrl(array $options = [])
    {
        return route('user.avatar', [...$options, 'user' => $this]);
    }

    /**
     * Gets the OAuth providers for this user.
     */
    public function oauthProviders(): HasMany
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Get the secret associated with the user.
     */
    public function oneTimePasscodeSecrets(): HasOne
    {
        return $this->hasOne(OneTimePasscodeSecret::class, 'user_id');
    }

    /**
     * Get the broadcast channel route definition that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannelRoute()
    {
        return 'App.Models.User.{id}';
    }

    /**
     * Interact with the slug.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->getAvatarUrl())->shouldCache();
    }

    /**
     * Gets users with roles
     *
     * @param  array  $roles  Role names or models
     * @param  bool  $hasAll  Specifies if users must have all or one of the roles
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public static function getUsersWithRoles($roles, bool $hasAll = true)
    {
        return self::whereHas('roles', function ($query) use ($roles, $hasAll) {
            if ($hasAll) {
                foreach ((array) $roles as $role) {
                    $roleName = $role instanceof Role ? $role->role : $role;

                    $query->where('role', $roleName);
                }
            } else {
                $query->whereIn('role', array_map(fn ($item) => $item instanceof Role ? $item->role : $item, (array) $roles));
            }
        })->get();
    }
}
