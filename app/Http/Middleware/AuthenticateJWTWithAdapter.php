<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;

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
    )
    {
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
     * @param  \Closure  $next
     * @param  string[]  ...$adapters
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, $name)
    {
        $adapter = $this->createAdapter($name);
        $this->auth->guard('jwt')->setAdapter($adapter);

        return $this->baseMiddleware->handle($request, $next, 'jwt');
    }

    /**
     * Creates Guard adapter.
     *
     * @param string $name
     * @return \LittleApps\LittleJWT\Contracts\GuardAdapter
     */
    protected function createAdapter($name) {
        return $this->app->make($name);
    }
}
