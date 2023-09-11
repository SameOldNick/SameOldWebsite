<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Tag extends Model
{
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
     * @var array<string>
     */
    protected $fillable = ['tag'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['slug'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Gets the article that have this tag.
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * Gets the projects that belong to this tag.
     *
     * @return mixed
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Interact with the slug.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?: Str::slug($attributes['tag']),
        )->shouldCache();
    }

    /**
     * Creates link to tag.
     *
     * @param bool $absolute
     * @return string
     */
    public function createLink(bool $absolute = true)
    {
        $query = sprintf('[%s]', $this->slug);

        return URL::route('blog.search', ['q' => $query], $absolute);
    }
}
