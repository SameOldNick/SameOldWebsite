<?php

namespace App\Components\OAuth\Contracts;

use Laravel\Socialite\Contracts\User as SocialiteUser;

interface OAuthFlowHandler
{
    /**
     * Handles OAuth redirect
     *
     * @return mixed
     */
    public function handleOAuthRedirect();

    /**
     * Handles OAuth callback
     *
     * @param SocialiteUser $socialiteUser
     * @return mixed Response
     */
    public function handleOAuthCallback(SocialiteUser $socialiteUser);
}
