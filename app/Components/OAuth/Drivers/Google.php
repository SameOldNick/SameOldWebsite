<?php

namespace App\Components\OAuth\Drivers;

class Google extends Driver
{
    protected function providerName(): string
    {
        return 'google';
    }

    protected function prepareRedirectResponse()
    {
        $this->provider()->scopes(['profile', 'email']);

        return $this;
    }
}
