<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories;

use App\Components\MFA\Concerns\InitializesOneTimePasscode;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\OneTimePasscode\Factory;
use OTPHP\OTPInterface;
use OTPHP\TOTP;

class TimebasedFactory implements Factory
{
    use InitializesOneTimePasscode;

    /**
     * {@inheritDoc}
     */
    public function create(string $secret): OTPInterface
    {
        return $this->initialize(TOTP::createFromSecret($secret), null);
    }

    /**
     * {@inheritDoc}
     */
    public function createForAuthenticatable(string $secret, MultiAuthenticatable $authenticatable): OTPInterface
    {
        return $this->initialize(TOTP::createFromSecret($secret), $authenticatable);
    }
}
