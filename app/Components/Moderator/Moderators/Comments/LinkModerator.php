<?php

namespace App\Components\Moderator\Moderators\Comments;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;

use function Safe\preg_match;

/**
 * @implements Moderator<Comment>
 */
class LinkModerator implements Moderator
{
    public function __construct(
        protected readonly array $config
    ) {}

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return (bool) $this->config['enabled'];
    }

    /**
     * {@inheritDoc}
     */
    public function moderate($comment): void
    {
        if (preg_match('/https?\:/i', $comment->comment)) {
            throw new FlagCommentException('The comment contains a URL.');
        }
    }
}
