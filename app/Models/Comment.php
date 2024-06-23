<?php

namespace App\Models;

use App\Traits\Models\Displayable;
use App\Traits\Models\Immutable;
use App\Traits\Models\Postable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Url\Url as SpatieUrl;

/**
 * @property int $id
 * @property string $title
 * @property string $comment
 * @property ?\Illuminate\Support\Carbon $approved_at
 * @property-read Article $article
 * @property-read ?Comment $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $children
 * @property-read ?User $approvedBy
 */
class Comment extends Model
{
    use Displayable;
    use HasFactory;
    use Immutable;
    use Postable;

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
        'comment',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'post',
        'article',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Gets the parent comment (if any)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Gets children of this comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Recursively gets all children of this comment.
     *
     * @return \Illuminate\Support\Collection<int, Comment>
     */
    public function allChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);

            if ($child->children->count() > 0) {
                $children->push($child->allChildren());
            }
        }

        return $children->flatten();
    }

    /**
     * Gets the Article this comments is for.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Checks if comment is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return ! is_null($this->approved_at) && $this->approved_at->isBefore(now());
    }

    /**
     * Gets the User who approved this comment (if any)
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Creates public link to this comment.
     *
     * @return string
     */
    public function createPublicLink(bool $absolute = true)
    {
        $params = ['comment' => $this];
        $fragment = $this->generateElementId();

        $url = SpatieUrl::fromString($this->article->createPublicLink($absolute, $params))->withFragment($fragment);

        return (string) $url;
    }

    /**
     * Creates temporary signed URL to this comment.
     *
     * @param  int  $minutes  Minutes until URL expires (default: 30)
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @return string
     */
    public function createPrivateUrl(int $minutes = 30, bool $absolute = true)
    {
        $params = ['comment' => $this];
        $fragment = $this->generateElementId();

        $url = SpatieUrl::fromString($this->article->createPrivateUrl($minutes, $absolute, $params))->withFragment($fragment);

        return (string) $url;
    }

    /**
     * Scope a query to only include approved comments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_by')->where('approved_at', '<=', now());
    }

    /**
     * Scope a query to only include parent comments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
