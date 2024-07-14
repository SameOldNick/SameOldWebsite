<?php

namespace App\Models;

use App\Enums\CommentStatus as CommentStatusEnum;
use App\Traits\Models\Displayable;
use App\Traits\Models\Immutable;
use App\Traits\Models\Postable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read ?Commenter $commenter
 * @property-read string $status One of STATUS_* constants
 * @property-read ?CommentStatus $lastStatus
 * @property-read ?string $email Email address of user who posted comment
 * @property-read string $display_name Display name of user who posted comment
 *
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 */
class Comment extends Model
{
    use Displayable;
    use HasFactory;
    use Immutable;
    use Postable;

    const STATUS_APPROVED = 'approved';

    const STATUS_FLAGGED = 'flagged';

    const STATUS_AWAITING_VERIFICATION = 'awaiting-verification';

    const STATUS_AWAITING_APPROVAL = 'awaiting-approval';

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
        'commenter',
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
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Gets children of this comment
     */
    public function children(): HasMany
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
     * Get the associated commenter.
     */
    public function commenter(): BelongsTo
    {
        return $this->belongsTo(Commenter::class);
    }

    /**
     * Gets flags for comment.
     */
    public function flags(): HasMany
    {
        return $this->hasMany(CommentFlag::class);
    }

    /**
     * Gets previous statuses for comment.
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(CommentStatus::class);
    }

    /**
     * Checks if comment is flagged.
     *
     * @return bool
     */
    public function isFlagged()
    {
        return $this->flags()->active()->count() > 0;
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
     * Gets the displayable name of the commenter
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->commenter?->display_name ?? $this->post->user?->getDisplayName());
    }

    /**
     * Gets the email of the user who posted the comment.
     */
    protected function email(): Attribute
    {
        return Attribute::get(fn () => $this->commenter?->email ?? $this->post->user?->email);
    }

    /**
     * Gets the status of the comment.
     */
    protected function status(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->isFlagged() => static::STATUS_FLAGGED,
            $this->commenter && ! $this->commenter->isVerified() => static::STATUS_AWAITING_VERIFICATION,
            ! $this->isApproved() => static::STATUS_AWAITING_APPROVAL,
            default => static::STATUS_APPROVED
        });
    /**
     * Gets the latest status of the comment.
     *
     * @return HasOne
     */
    public function lastStatus(): HasOne {
        return $this->hasOne(CommentStatus::class)->latest();
    }
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

    /**
     * Scope a query to only comment with email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithEmail($query, string $email)
    {
        return $query->whereHas('commenter', function ($query) use ($email) {
            $query->where('email', $email);
        });
    }
}
