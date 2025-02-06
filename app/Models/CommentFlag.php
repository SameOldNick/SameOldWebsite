<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $comment_id
 * @property string $reason
 * @property ?string $proposed
 * @property ?array $extra
 * @property ?int $deleted_by
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 * @property-read Comment $comment
 * @property-read ?User $deletedBy
 */
class CommentFlag extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'comment_id',
        'reason',
        'proposed',
        'extra',
        'deleted_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Gets the original comment.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Gets who deleted the flag.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Mutator for extra data
     */
    protected function extra(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ! empty($value) ? $this->fromJson($value) : [],
            set: fn ($value) => ! empty($value) ? $this->asJson($value) : null,
        );
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNull($this->getDeletedAtColumn());
    }
}
