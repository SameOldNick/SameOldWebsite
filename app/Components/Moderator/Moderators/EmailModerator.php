<?php

namespace App\Components\Moderator\Moderators;

use App\Components\Moderator\Concerns\CompilesList;
use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class EmailModerator implements Moderator
{
    use CompilesList;

    public function __construct(
        protected readonly array $config,
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
    public function moderate(Comment $comment): void
    {
        $email = $comment->email;

        if (is_null($email)) {
            return;
        }

        [,$domain] = $this->extractEmailParts($email);

        if (in_array($domain, $this->getAllowed())) {
            return;
        }

        if (in_array($domain, $this->getDenied())) {
            throw new FlagCommentException('E-mail address belongs to disposable email service.');
        }
    }

    /**
     * Extracts email parts from comment.
     *
     * @return array
     */
    protected function extractEmailParts(string $email)
    {
        return explode('@', strtolower($email), 2);
    }

    /**
     * Gets allowed domains
     */
    protected function getAllowed(): array
    {
        return $this->compileList($this->config['allow']);
    }

    /**
     * Gets denied domains
     */
    protected function getDenied(): array
    {
        // Cache the patterns for 24 hours
        return Cache::remember('email_deny_domains', 1440, function () {
            return $this->compileList($this->config['deny']);
        });
    }
}
