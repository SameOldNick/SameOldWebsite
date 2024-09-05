<?php

namespace App\Components\Moderator\Moderators\Comments;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;
use LanguageDetector\Language;
use LanguageDetector\LanguageDetector;

/**
 * @implements Moderator<Comment>
 */
class LanguageModerator implements Moderator
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
        $detected = $this->detectLanguage($comment->comment);

        if (! in_array($detected->getCode(), $this->getAllowed())) {
            throw new FlagCommentException($this->generateReason($comment, $detected));
        }
    }

    /**
     * Generates reason for denying comment
     *
     * @return string
     */
    protected function generateReason(Comment $comment, Language $detected)
    {
        return __($this->config['reason'] ?: 'Comments are restricted to specific languages.', [
            'comment' => $comment->comment,
            'detected' => $detected->getCode(),
        ]);
    }

    /**
     * Detects language of text
     */
    protected function detectLanguage(string $text): Language
    {
        $detector = new LanguageDetector;

        return $detector->evaluate($text)->getLanguage();
    }

    /**
     * Gets allowed language codes
     */
    protected function getAllowed(): array
    {
        return $this->config['allowed'] ?? [];
    }
}
