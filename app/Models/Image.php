<?php

namespace App\Models;

use App\Traits\Models\Fileable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;
    use Fileable;
    use HasUuids;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'description',
    ];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array<string>
     */
    protected $visible = [
        'uuid',
        'description',
        'file',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['file'];

    /**
     * Gets the articles this image belongs to
     *
     * @return BelongsToMany
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * Scope a query to only include images owned by a user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwned($query, User $user)
    {
        return $query->join('files', 'images.uuid', '=', 'files.fileable_id')->where('files.user_id', $user->getKey());
    }

    /**
     * Gets the alt-text for this image.
     *
     * @return Attribute
     */
    protected function alternativeText(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $attributes['description'] ? Str::squish($attributes['description']) : $this->file->pathinfo['filename']);
    }
}