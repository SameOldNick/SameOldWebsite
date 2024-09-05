<?php

namespace App\Components\Moderator\Moderators\Comments;

use App\Components\Moderator\Concerns\CompilesList;
use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;
use Illuminate\Support\Str;

use function Safe\preg_match;
use function Safe\preg_replace;

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
        $profanity = $this->findProfanity($comment->comment);

        if (empty($profanity)) {
            return;
        }

        $patterns = array_map(fn ($word) => sprintf('/%s/i', preg_quote($word)), $profanity);

        $updated = preg_replace($patterns, '[redacted]', $comment->comment);

        throw new FlagCommentException('Comments cannot contain profanity.', $updated);
    }

    /**
     * Change characters to UTF8 version
     */
    protected function normalizeText(string $text): string
    {
        return (string) Str::of($text)->ascii();
    }

    /**
     * Searches for matching profanity
     *
     * @return array Found words
     */
    protected function findProfanity(string $comment): array
    {
        $comment = strtolower($comment);
        $found = [];

        foreach ($this->getWords() as $word) {
            $pattern = sprintf('/\s%s\s/i', preg_quote($word));

            if (preg_match($pattern, $word)) {
                array_push($found, $word);
            }
        }

        return $found;
    }

    /**
     * Gets profane words
     */
    protected function getWords(): array
    {
        return $this->compileList($this->config['lists']);
    }
}
