<?php

namespace App\Models;

use App\Components\MFA\Concerns\HasOneTimePasscodeSecrets;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $country_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OAuthProvider> $oauthProviders
 */
class User extends Authenticatable implements MustVerifyEmail, MultiAuthenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasUuids;
    use HasOneTimePasscodeSecrets;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Checks if User has all specified roles
     *
     * @param array $roles Array of role names
     * @return bool
     */
    public function hasAllRoles(array $roles)
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
     * @param array $roles Array of role names to check.
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Gets files uploaded by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Gets comments approved by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class, 'approved_by');
    }

    /**
     * Gets refresh tokens for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Gets the country for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Gets the state for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
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
     * Gets readable state name
     *
     * @return string
     */
    public function stateReadable()
    {
        if ($this->isStateAssociated()) {
            return (string) $this->getRelationValue('state');
        } else {
            return $this->getAttributeValue('state');
        }
    }

    /**
     * Gets name to publicly display
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name ?? Str::before($this->email, '@');
    }

    /**
     * Gets the avatar URL for the user
     *
     * @param array $options
     * @return string
     */
    public function getAvatarUrl(array $options = [])
    {
        return route('user.avatar', [...$options, 'user' => $this]);
    }

    /**
     * Gets the OAuth providers for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Interact with the slug.
     *
     * @return Attribute
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->getAvatarUrl())->shouldCache();
    }

    /**
     * Gets users with roles
     *
     * @param array $roles Role names or models
     * @param bool $hasAll Specifies if users must have all or one of the roles
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
