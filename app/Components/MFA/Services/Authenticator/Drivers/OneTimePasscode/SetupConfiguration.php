<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use Illuminate\Contracts\Support\Arrayable;
use OTPHP\OTP;
use OTPHP\OTPInterface;

class SetupConfiguration implements Arrayable
{
    public function __construct(
        protected readonly OTPInterface $otp
    ) {}

    /**
     * Gets the account name.
     */
    public function getAccountName(): string
    {
        return $this->otp->getIssuer() !== null ? sprintf('%s:%s', $this->otp->getIssuer(), $this->otp->getLabel()) : $this->otp->getLabel();
    }

    /**
     * Gets the OTP URL.
     */
    public function getUrl(): string
    {
        return $this->otp->getProvisioningUri();
    }

    /**
     * Gets the secret.
     */
    public function getSecret(): string
    {
        return $this->otp->getSecret();
    }

    /**
     * Gets all the configuration settings.
     */
    public function getConfiguration(): array
    {
        return [
            'accountName' => $this->getAccountName(),
            'url' => $this->getUrl(),
            'secret' => $this->getSecret(),
        ];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getConfiguration();
    }
}
