<?php

namespace App\Components\Moderator\Moderators\Contact;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Exceptions\ContactFlagException;
use App\Models\ContactBlacklist;
use App\Models\ContactMessage;
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
        $name = $this->normalizeName($contactMessage->name);
        $email = $this->normalizeEmail($contactMessage->email);

        if ($this->isBlacklisted($name, $email, $this->config['ignoreCase'] ?? false)) {
            throw new ContactFlagException("The name '{$contactMessage->name}' or email address '{$contactMessage->email}' is banned.");
        }
    }

    protected function normalizeName(string $name)
    {
        // Transform multiple spaces or dashes into one space
        $name = preg_replace('/([\s+\-]+)/', ' ', $name);

        // Change to loweracse
        return Str::lower($name);
    }

    protected function normalizeEmail(string $email)
    {
        // Remove any + suffixes in email user
        $email = preg_replace('/\+[^@]+(?=@)/', '', $email);

        // Change to loweracse
        return Str::lower($email);
    }

    protected function isBlacklisted(string $name, string $email, bool $ignoreCase)
    {
        foreach (ContactBlacklist::all() as $model) {
            if ($model->matches($model->input === 'name' ? $name : $email, $ignoreCase)) {
                return true;
            }
        }

        return false;
    }
}
