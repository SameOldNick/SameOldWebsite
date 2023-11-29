<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * @var array<int, string>
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
     * @var array
     */
    protected $with = ['roles', 'state', 'country', 'oauthProviders'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
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
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Checks if User has specified roles
     *
     * @param array $roles Array of role names
     * @return bool
     */
    public function hasRoles(array $roles)
    {
        return $this->roles()->where(function ($query) use ($roles) {
            foreach ($roles as $role) {
                $query->where('role', $role);
            }
        })->count() > 0;
    }

    /**
     * Gets posts created by this user.
     *
     * @return mixed
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Gets comments approved by this user.
     *
     * @return mixed
     */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class, 'approved_by');
    }

    /**
     * Gets refresh tokens for this user.
     *
     * @return mixed
     */
    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Gets the country for this user
     *
     * @return mixed
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Gets the state for this user
     *
     * @return mixed
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

    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Interact with the slug.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->getAvatarUrl())->shouldCache();
    }
}
