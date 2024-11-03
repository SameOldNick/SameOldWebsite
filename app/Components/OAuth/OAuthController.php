<?php

namespace App\Components\OAuth;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthController
{
    /**
     * Initializes controller
     */
    public function __construct(
        protected readonly OAuth $oAuth
    ) {}

    /**
     * Handles redirect to OAuth provider
     *
     * @return mixed
     */
    public function handleRedirect(Request $request, string $provider)
    {
        $request->session()->reflash();

        $driver = $this->resolveProvider($provider);

        if (! $driver) {
            throw new NotFoundHttpException;
        }

        return $driver->handleRedirect();
    }

    /**
     * Handles callback response from OAuth provider
     *
     * @return mixed
     */
    public function handleCallback(Request $request, string $provider)
    {
        $request->session()->reflash();

        $driver = $this->resolveProvider($provider);

        if (! $driver) {
            throw new NotFoundHttpException;
        }

        return $driver->handleCallback();
    }

    /**
     * Resolves OAuth driver
     *
     * @return Providers\Provider|null
     */
    protected function resolveProvider(string $name)
    {
        try {
            $provider = $this->oAuth->provider($name);

            if ($provider->isConfigured()) {
                return $provider;
            }
        } catch (InvalidArgumentException $ex) {
        }

        return null;
    }
}
