<?php

namespace App\Components\Moderator\Moderators;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Support\Str;
use LanguageDetector\Language;
use LanguageDetector\LanguageDetector;

class LanguageModerator implements Moderator {
    public function __construct(
        protected readonly array $config
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return (bool) $this->config['enabled'];
    }

    /**
     * @inheritDoc
     */
    public function moderate(Comment $comment): void
    {
        $detected = $this->detectLanguage($comment->comment);

        if (!in_array($detected->getCode(), $this->getAllowed()))
            throw new FlagCommentException($this->generateReason($comment, $detected));
    }

    /**
     * Generates reason for denying comment
     *
     * @param Comment $comment
     * @param Language $detected
     * @return string
     */
    protected function generateReason(Comment $comment, Language $detected) {
        return __($this->config['reason'] ?: 'Comments are restricted to specific languages.', [
            'comment' => $comment->comment,
            'detected' => $detected->getCode()
        ]);
    }

    /**
     * Detects language of text
     *
     * @param string $text
     * @return Language
     */
    protected function detectLanguage(string $text): Language {
        $detector = new LanguageDetector();

        return $detector->evaluate($text)->getLanguage();
    }

    /**
     * Gets allowed language codes
     *
     * @return array
     */
    protected function getAllowed(): array {
        return $this->config['allowed'] ?? [];
    }
}
