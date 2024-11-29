<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use Closure;
use OTPHP\TOTP;

final class OneTimeAuthenticatable implements MultiAuthenticatable
{
    public function __construct(
        protected readonly Closure $resolver
    ) {}

    /**
     * Resolves secret
     *
     * @return string
     */
    public function resolveSecret()
    {
        return call_user_func($this->resolver);
    }

    /**
     * Creates authenticatable with random secret.
     */
    public static function generate(): static
    {
        // TOTP and HOTP use the same algorithm to generate secrets.
        $totp = TOTP::generate();

        return new self(fn () => $totp->getSecret());
    }

    /**
     * Creates authenticatable from string.
     */
    public static function string(string $secret): static
    {
        return new static(fn () => $secret);
    }
}
