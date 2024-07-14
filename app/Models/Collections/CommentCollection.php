<?php

namespace App\Models\Collections;

use App\Enums\CommentStatus;
use App\Models\Comment;
use BackedEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * @extends Collection<int, Comment>
 */
class CommentCollection extends Collection
{
    /**
     * Gets comments with status(es)
     *
     * @param CommentStatus|CommentStatus[]|string|string[] $statuses
     * @return static
     */
    public function status($statuses) {
        $statuses = array_map(fn ($status) => $status instanceof BackedEnum ? $status->value : $status, is_array($statuses) ? $statuses : [$statuses]);

        return $this->whereIn('status', $statuses);
    }

    /**
     * Gets the comments that are viewable
     *
     * @return static
     */
    public function viewable() {
        return $this->filter(fn (Comment $comment) => Gate::allows('view', $comment));
    }
}
