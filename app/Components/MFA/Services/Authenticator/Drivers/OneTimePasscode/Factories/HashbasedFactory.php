<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories;

use App\Components\MFA\Concerns\InitializesOneTimePasscode;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\OneTimePasscode\Factory;
use OTPHP\HOTP;
use OTPHP\OTPInterface;

class HashbasedFactory implements Factory
{
    use InitializesOneTimePasscode;

    /**
     * @inheritDoc
     */
    public function create(string $secret): OTPInterface
    {
        return $this->initialize(HOTP::createFromSecret($secret), null);
    }

    /**
     * @inheritDoc
     */
    public function createForAuthenticatable(string $secret, MultiAuthenticatable $authenticatable): OTPInterface
    {
        return $this->initialize(HOTP::createFromSecret($secret), $authenticatable);
    }
}
