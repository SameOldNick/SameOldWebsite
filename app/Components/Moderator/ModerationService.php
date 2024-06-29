<?php

namespace App\Components\Moderator;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;
use App\Models\CommentFlag;

class ModerationService
{
    /**
     * Initializes moderation service
     */
    public function __construct(
        public readonly ModeratorsFactory $factory,
    ) {}

    /**
     * Moderates comment
     *
     * @param  Comment  $comment  Comment to moderate
     * @return bool True if comment was flagged.
     */
    public function moderate(Comment $comment)
    {
        $flags = [];

        foreach ($this->getModerators() as $moderator) {
            if (! $moderator->isEnabled()) {
                continue;
            }

            try {
                $moderator->moderate($comment);
            } catch (FlagCommentException $ex) {
                array_push($flags, $this->exceptionToFlag($ex));
            }
        }

        $comment->flags()->saveMany($flags);

        return ! empty($flags);
    }

    /**
     * Gets moderators
     *
     * @return Moderator[]
     */
    public function getModerators(): array
    {
        return $this->factory->build();
    }

    /**
     * Transforms FlagCommentException to CommentFlag
     *
     * @return CommentFlag
     */
    protected function exceptionToFlag(FlagCommentException $ex)
    {
        $flag = new CommentFlag([
            'reason' => $ex->getMessage(),
            'proposed' => $ex->getProposed(),
            'extra' => $ex->getExtra(),
        ]);

        return $flag;
    }
}
