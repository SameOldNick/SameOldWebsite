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
            ], function () {
                /** @var \Illuminate\Routing\Router $this */
                $this->get('redirect/{driver}', [OAuthController::class, 'handleRedirect'])->name('oauth.redirect');
                $this->get('callback/{driver}', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
            });
        };
    }
}
