<?php

namespace App\Components\Moderator\Contracts;

use App\Http\Requests\CommentRequest;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;

interface Moderator {
    /**
     * Determines if moderator is enabled
     *
     * @return boolean
     */
    public function isEnabled(): bool;

    /**
     * Moderates a comment
     *
     * @param Comment $comment
     * @return void
     * @throws FlagCommentException Thrown if comment should be flagged.
     */
    public function moderate(Comment $comment): void;
}
