<?php

namespace App\Components\OAuth\Drivers;

use Laravel\Socialite\Contracts\User as SocialiteUser;

class Twitter extends Driver
{
    protected function providerName(): string
    {
        return 'twitter';
    }

    protected function prepareRedirectResponse()
    {
        $this->provider()->scopes(['users.read']);

        return $this;
    }

    /**
     * Prepares callback response.
     * Example: Create and login user.
     *
     * @return $this
     */
    protected function prepareCallbackResponse(SocialiteUser $socialiteUser)
    {
        if (empty($socialiteUser->getEmail())) {
            $socialiteUser->email = $this->generateEmail($socialiteUser);
        }

        return parent::prepareCallbackResponse($socialiteUser);
    }

    protected function generateEmail(SocialiteUser $socialiteUser)
    {
        return sprintf('%s@x.com', $socialiteUser->getId());
    }
}
