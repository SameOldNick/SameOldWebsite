<?php

namespace App\Components\Moderator\Moderators\Contact;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\ContactFlagException;
use App\Models\ContactMessage;
use App\Models\EmailBlacklist;
use Illuminate\Support\Str;

/**
 * @implements Moderator<ContactMessage>
 */
class BlacklistModerator implements Moderator
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
    public function moderate($contactMessage): void
    {
        $email = $this->normalizeEmail($contactMessage->email);

        if ($this->isBlacklisted($email)) {
            throw new ContactFlagException("The email address {$email} is banned.");
        }
    }

    protected function normalizeEmail(string $email)
    {
        // Remove any + suffixes in email user
        $email = preg_replace('/\+[^@]+(?=@)/', '', $email);

        // Change to loweracse
        return Str::lower($email);
    }

    protected function isBlacklisted(string $email)
    {
        return EmailBlacklist::where('email', $email)->count() > 0;
    }
}
