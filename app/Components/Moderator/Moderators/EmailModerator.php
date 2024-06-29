<?php

namespace App\Components\Moderator\Moderators;

use App\Components\Moderator\Concerns\CompilesList;
use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\CommentNotAllowedException;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\DisposableEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class EmailModerator implements Moderator {
    use CompilesList;

    public function __construct(
        protected readonly array $config,
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
        $email = $comment->email;

        if (is_null($email))
            return;

        [,$domain] = $this->extractEmailParts($email);

        if (in_array($domain, $this->getAllowed()))
            return;

        if (in_array($domain, $this->getDenied()))
            throw new FlagCommentException('E-mail address belongs to disposable email service.');
    }

    /**
     * Extracts email parts from comment.
     *
     * @param string $email
     * @return array
     */
    protected function extractEmailParts(string $email) {
        return explode('@', strtolower($email), 2);
    }

    /**
     * Gets allowed domains
     *
     * @return array
     */
    protected function getAllowed(): array {
        return $this->compileList($this->config['allow']);
    }

    /**
     * Gets denied domains
     *
     * @return array
     */
    protected function getDenied(): array {
        // Cache the patterns for 24 hours
        return Cache::remember('email_deny_domains', 1440, function () {
            return $this->compileList($this->config['deny']);
        });
    }
}
