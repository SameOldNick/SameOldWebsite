<?php

namespace App\Components\OAuth;

class OAuthRouteMethods
{
    public function oauth()
    {
        return function ($options = []) {
            /** @var \Illuminate\Routing\Router $this */
            $this->group([
                'namespace' => class_exists($this->prependGroupNamespace('Auth\OAuthController')) ? null : 'App\Http\Controllers',
                'prefix' => 'oauth',
            ], function () use ($options) {
                /** @var \Illuminate\Routing\Router $this */
                $oauth = $this->container->make(OAuth::class);

                foreach ($oauth->configured() as $driver) {
                    $oauth->driver($driver)->registerRoutes($this);
                }
            });
        };
    }
}
