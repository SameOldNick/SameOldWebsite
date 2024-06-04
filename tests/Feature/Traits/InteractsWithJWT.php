<?php

namespace Tests\Feature\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use LittleApps\LittleJWT\JWT\JWT;

trait InteractsWithJWT
{
    /**
     * Includes JWT in HTTP requests
     *
     * @param  string|JWT  $token
     * @return static
     */
    public function withJwt($token)
    {
        return $this->withHeader('Authorization', sprintf('Bearer %s', (string) $token));
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param  string|null  $driver
     * @return $this
     */
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $accessToken = $this->app['auth']->guard('jwt')->buildJwtForUser($user);

        $this->app['auth']->guard($driver)->setUser($user);
        $this->app['auth']->shouldUse($driver);

        return $this->withJwt($accessToken);
    }
}
