<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentFlag extends Model
{
    use HasFactory;
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Gets who approved the flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
