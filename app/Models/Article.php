<?php

namespace App\Models;

use App\Models\Collections\ArticleCollection;
use App\Models\Presenters\ArticlePresenter;
use App\Traits\Models\HasPresenter;
use App\Traits\Models\Postable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property ?\Illuminate\Support\Carbon $published_at
 * @property-read bool $is_published
 * @property-read bool $is_scheduled
 * @property-read Revision $revision
 * @property-read ?Revision $currentRevision
 * @property-read ?Image $mainImage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Revision> $revisions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read \App\Models\Collections\CommentCollection $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 *
 * @method static \Illuminate\Database\Eloquent\Builder published()
 * @method static \Illuminate\Database\Eloquent\Builder sortedByPublishDate()
 * @method static \Database\Factories\ArticleFactory factory($count = null, $state = [])
 */
class Article extends Model
{
    use HasFactory;

    /** @use HasPresenter<ArticlePresenter> */
    use HasPresenter;

    use Postable;
    use SoftDeletes;

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
    protected $fillable = [
        'title',
        'slug',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var list<string>
     */
    protected $with = [
        'mainImage',
        'currentRevision',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'revision',
    ];

    /**
     * The presenter class
     *
     * @var class-string
     */
    protected static ?string $presenter = ArticlePresenter::class;


    /**
     * Create a new Eloquent Collection instance.
     *
     * @return ArticleCollection
     */
    public function newCollection(array $models = [])
    {
        return new ArticleCollection($models);
    }

    /**
     * Gets the current revision
     */
    public function currentRevision(): BelongsTo
    {
        return $this->belongsTo(Revision::class, 'current_revision');
    }

    /**
     * Gets the revisions of this article
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    /**
     * Gets the tags that this article has.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Gets comments for this article.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Gets the images that belong to this article.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }

    /**
     * Gets the main image (if any)
     */
    public function mainImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'main_image', 'uuid');
    }

    /**
     * Scope a query to only include published articles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Scope a query to sort by published_at column value.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortedByPublishDate($query)
    {
        return $query->latest('published_at');
    }

    /**
     * Gets the revision for this article.
     */
    protected function revision(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->currentRevision) ? $this->currentRevision : $this->revisions()->latest()->first());
    }

    /**
     * Checks if article is published.
     */
    protected function isPublished(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->published_at) && $this->published_at->isPast());
    }

    /**
     * Checks if article is scheduled to be published.
     */
    protected function isScheduled(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->published_at) && $this->published_at->isFuture());
    }
}
