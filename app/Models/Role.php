<?php

namespace App\Models;

use App\Models\Collections\RoleCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $role
 * @property-read string $readable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 */
class Role extends Model
{
    use Concerns\HidesPrimaryKey;
    use HasFactory;

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
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array<int, \Illuminate\Database\Eloquent\Model>  $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new RoleCollection($models);
    }
}
