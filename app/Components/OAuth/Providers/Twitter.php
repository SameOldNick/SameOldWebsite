<?php

namespace App\Components\OAuth\Providers;

use App\Components\OAuth\Contracts\OAuthFlowHandler;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class Twitter extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function providerName(): string
    {
        return 'twitter';
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareRedirect(OAuthFlowHandler $handler): OAuthFlowHandler
    {
        $this->provider()->scopes(['users.read']);

        return $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareSocialUser(SocialiteUser $socialUser): SocialiteUser
    {
        // The email address maybe empty with X
        if (empty($socialUser->getEmail())) {
            // Instead, come up with e-mail address
            $socialUser->email = $this->generateEmail($socialUser);
        }

        return $socialUser;
    }

    /**
     * Generates email address to use when the email field is missing.
     */
    protected function generateEmail(SocialiteUser $socialiteUser): string
    {
        return sprintf('%s@x.com', $socialiteUser->getId());
    }
}
