<?php

namespace App\Components\MFA\Concerns;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use OTPHP\OTPInterface;

trait InitializesOneTimePasscode
{
    /**
     * Initializes an OTPInterface and associates authenticatable (if specified)
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
     */
    protected function getIssuer(): string
    {
        return config('app.name', __('Unknown Issuer'));
    }

    /**
     * Gets the label for the OTP
     */
    protected function getLabel(MultiAuthenticatable $authenticatable): string
    {
        return method_exists($authenticatable, 'getOneTimePasscodeLabel') ? $authenticatable->getOneTimePasscodeLabel() : $authenticatable->email;
    }
}
