<?php

namespace App\Components\MFA\Rules;

use App\Components\MFA\Contracts\AuthServiceInterface;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CurrentAuthCode implements ValidationRule
{
    public function __construct(
        protected ?MultiAuthenticatable $authenticatable = null,
        protected ?AuthServiceInterface $service = null,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->getService()->verifyCode($this->getAuthenticatable(), $value)) {
            $fail('Multi-factor authentication code is incorrect.');
        }
    }

    /**
     * Gets the authenticator service.
     *
     * @return AuthServiceInterface
     */
    protected function getService()
    {
        return $this->service ?? app('mfa.auth');
    }

    /**
     * Gets the authenticatable.
     *
     * @return MultiAuthenticatable
     */
    protected function getAuthenticatable()
    {
        return $this->authenticatable ?? auth()->user();
    }
}
