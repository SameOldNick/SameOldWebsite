<?php

namespace App\Models;

use App\Enums\CommentStatus as CommentStatusEnum;
use App\Enums\CommentUserType;
use App\Models\Collections\CommentCollection;
use App\Traits\Models\Displayable;
use App\Traits\Models\Immutable;
use App\Traits\Models\Postable;
use BackedEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Url\Url as SpatieUrl;

/**
 * @property int $id
 * @property string $title
 * @property string $comment
 * @property-read Article $article
 * @property-read ?Comment $parent
 * @property-read CommentCollection $children
 * @property-read ?Commenter $commenter
 * @property-read ?CommentStatus $lastStatus
 * @property-read ?User $marked_by
 * @property-read string $status {@see CommentStatusEnum}
 * @property-read string $user_type {@see CommentUserType}
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
        'statuses',
        'children',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'status',
        'user_type',
        'marked_by',
        'commenter_info',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @return CommentCollection
     */
    public function newCollection(array $models = [])
    {
        return new CommentCollection($models);
    }

    /**
     * Gets the parent comment (if any)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Gets all parents of comment.
     */
    public function allParents()
    {
        $parents = [];

        $parent = $this->parent;

        while (! is_null($parent)) {
            array_push($parents, $parent);

            $parent = $parent->parent;
        }

        return $this->newCollection($parents);
    }

    /**
     * Gets children of this comment
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Gets all children of this comment.
     */
    public function allChildren()
    {
        $all = [];

        foreach ($this->children as $child) {
            array_push($all, ...static::collectChildren($child));
        }

        return $this->newCollection($all);
    }

    /**
     * Recursively collects all children of comment (including passed comment).
     *
     * @return array
     */
    public static function collectChildren(self $comment)
    {
        $all = [$comment];

        foreach ($comment->children as $child) {
            array_push($all, ...static::collectChildren($child));
        }

        return $all;
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
     * Gets the commenter info
     */
    protected function commenterInfo(): Attribute
    {
        return Attribute::get(fn () => [
            'display_name' => $this->commenter?->display_name ?? $this->post->user?->getDisplayName(),
            'email' => $this->commenter?->email ?? $this->post->user?->email,
        ]);
    }

    /**
     * Determines current status of comment.
     */
    public function determineStatus(): BackedEnum
    {
        if ($this->lastStatus?->status) {
            return $this->lastStatus->status;
        }

        return match (true) {
            $this->isFlagged() => CommentStatusEnum::Flagged,
            $this->commenter && $this->commenter->isVerified() => CommentStatusEnum::AwaitingApproval,
            $this->commenter && ! $this->commenter->isVerified() => CommentStatusEnum::AwaitingVerification,
            $this->post->user && ! $this->post->user->hasVerifiedEmail() => CommentStatusEnum::AwaitingVerification,
            $this->post->user && $this->post->user->hasVerifiedEmail() => CommentStatusEnum::AwaitingApproval,
            default => CommentStatusEnum::AwaitingApproval
        };
    }

    /**
     * Gets the latest status of the comment.
     */
    public function lastStatus(): HasOne
    {
        return $this->hasOne(CommentStatus::class)->latest();
    }

    /**
     * Gets the type of user who posted the comment (one of CommentUserType case values)
     */
    protected function userType(): Attribute
    {
        return Attribute::get(fn () => $this->post->user ? CommentUserType::Registered->value : CommentUserType::Guest->value);
    }

    /**
     * Gets the status of the comment.
     */
    protected function status(): Attribute
    {
        return Attribute::get(fn () => $this->determineStatus()->value);
    }

    /**
     * Gets who set the status.
     */
    protected function markedBy(): Attribute
    {
        return Attribute::get(fn () => $this->lastStatus?->user);
    }

    /**
     * Gets the avatar URL using either the registered user or guest
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->post?->user ? $this->post->user->avatar_url : $this->commenter->avatar_url)->shouldCache();
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
     * Scope a query to only include comments with status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->afterQuery(fn ($comments) => $comments->status($status));
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
