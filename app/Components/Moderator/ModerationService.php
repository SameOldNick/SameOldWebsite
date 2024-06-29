<?php

namespace App\Components\Moderator;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\CommentFlag;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

class ModerationService {
    /**
     * Initializes moderation service
     *
     * @param ModeratorsFactory $factory
     */
    public function __construct(
        public readonly ModeratorsFactory $factory,
    )
    {

    }

    /**
     * Moderates comment
     *
     * @param Comment $comment Comment to moderate
     * @return bool True if comment was flagged.
     */
    public function moderate(Comment $comment) {
        $flags = [];

        foreach ($this->getModerators() as $moderator) {
            if (!$moderator->isEnabled())
                continue;

            try {
                $moderator->moderate($comment);
            } catch (FlagCommentException $ex) {
                array_push($flags, $this->exceptionToFlag($ex));
            }
        }

        $comment->flags()->saveMany($flags);

        return !empty($flags);
    }

    /**
     * Gets moderators
     *
     * @return Moderator[]
     */
    public function getModerators(): array {
        return $this->factory->build();
    }

    /**
     * Transforms FlagCommentException to CommentFlag
     *
     * @param FlagCommentException $ex
     * @return CommentFlag
     */
    protected function exceptionToFlag(FlagCommentException $ex) {
        $flag = new CommentFlag([
            'reason' => $ex->getMessage(),
            'proposed' => $ex->getProposed(),
            'extra' => $ex->getExtra()
        ]);

        return $flag;
    }
}
