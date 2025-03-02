<?php

namespace App\Components\Captcha\Drivers\Recaptcha\Testing;

use App\Components\Captcha\Drivers\Recaptcha\UserResponse;
use App\Components\Captcha\Drivers\Recaptcha\Verifier as RecaptchaVerifier;
use Closure;
use Illuminate\Http\Client\Response;

class Verifier extends RecaptchaVerifier
{
    protected ?Closure $requestCallback = null;

    /**
     * Gets the site key.
     *
     * @return void
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * Specifies a callback to be used for handling the request.
     */
    public function useRequestCallback(?Closure $callback): static
    {
        $this->requestCallback = $callback;

        return $this;
    }

    /**
     * Performs the request.
     */
    protected function performRequest(UserResponse $userResponse): Response
    {
        if ($this->requestCallback) {
            return call_user_func($this->requestCallback, $userResponse);
        }

        return parent::performRequest($userResponse);
    }
}
