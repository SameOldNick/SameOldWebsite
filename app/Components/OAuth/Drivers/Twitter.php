<?php

namespace App\Components\OAuth\Drivers;

use App\Components\OAuth\Contracts\OAuthFlowHandler;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class Twitter extends Driver
{
    /**
     * @inheritDoc
     */
    public function providerName(): string
    {
        return 'twitter';
    }

    /**
     * @inheritDoc
     */
    protected function prepareRedirect(OAuthFlowHandler $handler): OAuthFlowHandler
    {
        $this->provider()->scopes(['users.read']);

        return $handler;
    }

    /**
     * @inheritDoc
     */
    protected function prepareCallback(OAuthFlowHandler $handler, SocialiteUser $socialUser): OAuthFlowHandler
    {
        // The email address maybe empty with X
        if (empty($socialUser->getEmail())) {
            // Instead, come up with e-mail address
            $socialUser->email = $this->generateEmail($socialUser);
        }

        return $handler;
    }

    /**
     * Generates email address to use when the email field is missing.
     *
     * @param SocialiteUser $socialiteUser
     * @return string
     */
    protected function generateEmail(SocialiteUser $socialiteUser): string
    {
        return sprintf('%s@x.com', $socialiteUser->getId());
    }
}
