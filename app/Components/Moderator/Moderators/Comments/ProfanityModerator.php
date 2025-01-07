<?php

namespace App\Components\Moderator\Moderators\Comments;

use App\Components\Moderator\Concerns\CompilesList;
use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;
use Arifrh\ProfanityFilter\Check;

/**
 * @implements Moderator<Comment>
 */
class ProfanityModerator implements Moderator
{
    use CompilesList;

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
        $check = new Check($this->getWords());

        $cleaned = $check->cleanWords($comment->comment);

        if ($cleaned !== $comment->comment) {
            throw new FlagCommentException('Comments cannot contain profanity.', $cleaned);
        }
    }

    /**
     * Gets profane words
     */
    protected function getWords(): array
    {
        return $this->compileList($this->config['lists']);
    }
}
