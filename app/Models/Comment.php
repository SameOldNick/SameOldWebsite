<?php

namespace App\Models;

use App\Traits\Models\Displayable;
use App\Traits\Models\Postable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    use Postable;
    use Displayable;

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
    protected $fillable = [
        'title',
        'comment',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'post',
        'article',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Gets the parent comment (if any)
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Gets children of this comment
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Recursively gets all children of this comment.
     *
     * @return \Illuminate\Support\Collection
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
     *
     * @return mixed
     */
    public function article()
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
     *
     * @return mixed
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
