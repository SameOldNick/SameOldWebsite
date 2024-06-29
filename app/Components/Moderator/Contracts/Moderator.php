<?php

namespace App\Components\Moderator\Contracts;

use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;

interface Moderator
{
    /**
     * Determines if moderator is enabled
     */
    public function isEnabled(): bool;

    /**
     * Moderates a comment
     *
     * @throws FlagCommentException Thrown if comment should be flagged.
     */
    public function moderate(Comment $comment): void;
}
