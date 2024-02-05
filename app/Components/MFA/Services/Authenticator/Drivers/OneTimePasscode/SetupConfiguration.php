<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\OneTimePasscode\Factory;
use App\Components\MFA\Facades\MFA;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\HashbasedFactory;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimeAuthenticatable;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimePasscode;
use OTPHP\OTPInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Str;
use OTPHP\OTP;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\SecretResolver;

class SetupConfiguration implements Arrayable {
    public function __construct(
        protected readonly OTPInterface $otp
    )
    {
    }

    /**
     * Gets the account name.
     *
     * @return string
     */
    public function getAccountName(): string
    {
        return $this->otp->getIssuer() !== null ? sprintf('%s:%s', $this->otp->getIssuer(), $this->otp->getLabel()) : $this->otp->getLabel();
    }

    /**
     * Gets the OTP URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->otp->getProvisioningUri();
    }

    /**
     * Gets the secret.
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->otp->getSecret();
    }

    /**
     * Gets all the configuration settings.
     *
     * @return array
     */
    public function getConfiguration(): array {
        return [
            'accountName' => $this->getAccountName(),
            'url' => $this->getUrl(),
            'secret' => $this->getSecret()
        ];
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return $this->getConfiguration();
    }
}
