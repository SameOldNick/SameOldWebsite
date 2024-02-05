<?php

namespace App\Components\MFA\Concerns;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use OTPHP\OTPInterface;

trait InitializesOneTimePasscode
{
    /**
     * Initializes an OTPInterface and associates authenticatable (if specified)
     *
     * @param OTPInterface $otp
     * @param MultiAuthenticatable|null $authenticatable
     * @return OTPInterface
     */
    protected function initialize(OTPInterface $otp, ?MultiAuthenticatable $authenticatable): OTPInterface
    {
        if (! is_null($authenticatable)) {
            $otp->setIssuer($this->getIssuer());
            $otp->setLabel($this->getLabel($authenticatable));
        } else {
            $otp->setLabel($this->getIssuer());
        }

        return $otp;
    }

    /**
     * Gets the issuer for the OTP
     *
     * @return string
     */
    protected function getIssuer(): string
    {
        return config('app.name', __('Unknown Issuer'));
    }

    /**
     * Gets the label for the OTP
     *
     * @param MultiAuthenticatable $authenticatable
     * @return string
     */
    protected function getLabel(MultiAuthenticatable $authenticatable): string
    {
        return method_exists($authenticatable, 'getOneTimePasscodeLabel') ? $authenticatable->getOneTimePasscodeLabel() : $authenticatable->email;
    }
}
