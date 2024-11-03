<?php

namespace App\Components\OAuth\Drivers;

use App\Components\OAuth\Contracts\OAuthFlowHandler;

class GitHub extends Driver
{
    /**
     * {@inheritDoc}
     */
    public function providerName(): string
    {
        return 'github';
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareRedirect(OAuthFlowHandler $handler): OAuthFlowHandler
    {
        $this->provider()->scopes(['read:user']);

        return $handler;
    }
}
