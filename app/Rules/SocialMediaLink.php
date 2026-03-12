<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SocialMediaLink implements ValidationRule
{
    protected $domains = [
        'facebook.com',
        'twitter.com',
        'x.com',
        'linkedin.com',
        'github.com',
    ];

    protected $requireSchema;

    public function __construct(bool $requireSchema = false)
    {
        $this->requireSchema = $requireSchema;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match($this->pattern(), $value)) {
            $fail(sprintf('Link does not start with any of the following domains: %s', implode(', ', $this->domains)));
        }
    }

    private function pattern()
    {
        return sprintf('/^(https?:\/\/)%s(www\.)?(%s)/i', $this->requireSchema ? '?' : '', implode('|', array_map('preg_quote', $this->domains)));
    }
}
