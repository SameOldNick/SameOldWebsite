<?php

namespace App\Components\OAuth\Contracts;

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
     * @return mixed Response
     */
    public function handleOAuthCallback();
}
