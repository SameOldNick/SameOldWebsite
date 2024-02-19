<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Exceptions\MultiAuthNotConfiguredException;
use Closure;
use OTPHP\TOTP;

class OneTimeAuthenticatable implements MultiAuthenticatable
{
    public function __construct(
        protected readonly Closure $resolver
    ) {
    }

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
     *
     * @return static
     */
    public static function generate(): static
    {
        // TOTP and HOTP use the same algorithm to generate secrets.
        $totp = TOTP::generate();

        return new static(fn () => $totp->getSecret());
    }

    /**
     * Creates authenticatable from string.
     *
     * @param string $secret
     * @return static
     */
    public static function string(string $secret): static
    {
        return new static(fn () => $secret);
    }

    /**
     * Creates authenticatable for authentication.
     *
     * @param MultiAuthenticatable $authenticatable
     * @return static
     */
    public static function auth(MultiAuthenticatable $authenticatable): static
    {
        return new static(fn () => optional($authenticatable->oneTimePasscodeSecrets)->auth_secret ?? MultiAuthNotConfiguredException::throw());
    }

    /**
     * Creates authenticatable for backup.
     *
     * @param MultiAuthenticatable $authenticatable
     * @return static
     */
    public static function backup(MultiAuthenticatable $authenticatable): static
    {
        return new static(fn () => optional($authenticatable->oneTimePasscodeSecrets)->backup_secret ?? MultiAuthNotConfiguredException::throw());
    }
}