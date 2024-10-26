<?php

namespace App\Components\OAuth;

use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthController
{
    /**
     * Initializes controller
     *
     * @param OAuth $oAuth
     */
    public function __construct(
        protected readonly OAuth $oAuth
    ) {}

    /**
     * Handles redirect to OAuth provider
     *
     * @param string $driver
     * @return mixed
     */
    public function handleRedirect(string $driver)
    {
        $driver = $this->resolveDriver($driver);

        if (!$driver) {
            throw new NotFoundHttpException();
        }

        return $driver->handleRedirect();
    }

    /**
     * Handles callback response from OAuth provider
     *
     * @param string $driver
     * @return mixed
     */
    public function handleCallback(string $driver)
    {
        $driver = $this->resolveDriver($driver);

        if (!$driver) {
            throw new NotFoundHttpException();
        }

        return $driver->handleRedirect();
    }

    /**
     * Resolves OAuth driver
     *
     * @param string $name
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
