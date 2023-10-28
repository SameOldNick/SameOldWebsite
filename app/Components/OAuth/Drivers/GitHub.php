<?php

namespace App\Components\OAuth\Drivers;

class GitHub extends Driver
{
    protected function providerName(): string
    {
        return 'github';
    }

    protected function prepareRedirectResponse()
    {
        $this->provider()->scopes(['read:user']);

        return $this;
    }
}
