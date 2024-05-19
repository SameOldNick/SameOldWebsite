<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Contracts\Container\Container;

class AuthenticateJWTWithAdapter implements AuthenticatesRequests
{
    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct(
        private Container $app,
        private Auth $auth,
        private Authenticate $baseMiddleware,
    ) {
        //
    }

    public static function adapter(string $name)
    {
        return sprintf('%s:%s', static::class, $name);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Closure  $next
     * @param  string  $name
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, $name)
    {
        $this->getGuard()->setAdapter($this->createAdapter($name));

        return $this->baseMiddleware->handle($request, $next, 'jwt');
    }

    /**
     * Creates Guard adapter.
     *
     * @param string $name
     * @return \LittleApps\LittleJWT\Contracts\GuardAdapter
     */
    protected function createAdapter($name)
    {
        return $this->app->make($name);
    }

    /**
     * Gets JWT guard
     *
     * @return \LittleApps\LittleJWT\Guards\Guard
     */
    protected function getGuard() {
        return $this->auth->guard('jwt');
    }
}
