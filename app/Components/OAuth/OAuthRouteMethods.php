<?php

namespace App\Components\OAuth;

use Illuminate\Support\Arr;

class OAuthRouteMethods
{
    public function oauth()
    {
        return function ($options = []) {
            $this->group([
                'namespace' => class_exists($this->prependGroupNamespace('Auth\OAuthController')) ? null : 'App\Http\Controllers',
                'prefix' => 'oauth'
            ], function() use ($options) {
                $oauth = $this->container->make(OAuth::class);

                foreach ($oauth->configured() as $driver) {
                    $oauth->driver($driver)->registerRoutes($this);
                }
            });
        };
    }
}
