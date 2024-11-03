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
    public function handleRedirect(Request $request, string $driver)
    {
        $request->session()->reflash();

        $driver = $this->resolveDriver($driver);

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
    public function handleCallback(Request $request, string $driver)
    {
        $request->session()->reflash();

        $driver = $this->resolveDriver($driver);

        if (! $driver) {
            throw new NotFoundHttpException;
        }

        return $driver->handleCallback();
    }

    /**
     * Resolves OAuth driver
     *
     * @return Drivers\Driver|null
     */
    protected function resolveDriver(string $name)
    {
        try {
            $driver = $this->oAuth->driver($name);

            if ($driver->isConfigured()) {
                return $driver;
            }
        } catch (InvalidArgumentException $ex) {
        }

        return null;
    }
}
