<?php

namespace App\Components\OAuth\Drivers;

use App\Components\OAuth\Contracts\OAuthFlowHandler;

class Google extends Driver
{
    /**
     * @inheritDoc
     */
    public function providerName(): string
    {
        return 'google';
    }

    /**
     * @inheritDoc
     */
    public function prepareRedirect(OAuthFlowHandler $handler): OAuthFlowHandler
    {
        $this->provider()->scopes(['profile', 'email']);

        return $handler;
    }
}
