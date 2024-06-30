<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $tag
 * @property string $slug
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Article> $articles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Project> $projects
 *
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 */
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
     * @var list<string>
     */
    protected $fillable = ['tag'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['slug'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
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
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * Gets the projects that belong to this tag.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Interact with the slug.
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?: Str::slug($attributes['tag']),
            set: fn ($value) => $value
        )->shouldCache();
    }

    /**
     * Creates link to tag.
     *
     * @return string
     */
    public function createLink(bool $absolute = true)
    {
        $query = sprintf('[%s]', $this->slug);

        return URL::route('blog.search', ['q' => $query], $absolute);
    }
}
