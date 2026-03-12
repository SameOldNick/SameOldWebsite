<?php

namespace App\Components\OAuth;

use Illuminate\Routing\Router;

class OAuthRouteMethods
{
    public function oauth()
    {
        return function ($options = []) {
            /** @var Router $this */
            $this->group([
                'namespace' => class_exists($this->prependGroupNamespace('Auth\OAuthController')) ? null : 'App\Http\Controllers',
                'prefix' => 'oauth',
            ], function () {
                /** @var Router $this */
                $this->get('redirect/{provider}', [OAuthController::class, 'handleRedirect'])->name('oauth.redirect');
                $this->get('callback/{provider}', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
            });
        };
    }
}
