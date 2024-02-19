<?php

namespace App\Components\MFA\Contracts\OneTimePasscode;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use OTPHP\OTPInterface;

interface Factory
{
    /**
     * Creates instance of OTPInterface
     *
     * @param string $secret
     * @return OTPInterface
     */
    public function create(string $secret): OTPInterface;

    /**
     * Creates instance of OTPInterface for authenticatable
     *
     * @param string $secret
     * @param MultiAuthenticatable $authenticatable
     * @return OTPInterface
     */
    public function createForAuthenticatable(string $secret, MultiAuthenticatable $authenticatable): OTPInterface;
}