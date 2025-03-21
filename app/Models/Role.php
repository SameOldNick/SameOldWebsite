<?php

namespace App\Models;

use App\Models\Collections\RoleCollection;
use App\Traits\Models\HidesPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $role
 * @property-read string $readable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 */
#[CollectedBy(RoleCollection::class)]
class Role extends Model
{
    use HidesPrimaryKey;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['role'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = ['pivot'];

    /**
     * Gets the role name in readable form.
     */
    protected function readable(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Str::headline($attributes['role'])
        );
    }

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
