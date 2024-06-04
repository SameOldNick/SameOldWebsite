<?php

namespace App\Components\MFA\Contracts;

use Illuminate\Routing\Router;

interface AuthServiceInterface
{
    /**
     * Checks if MFA is configured for authenticatable.
     */
    public function isConfigured(MultiAuthenticatable $authenticatable): bool;

    /**
     * Verifies code for authenticatable.
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool;

    /**
     * Registers routes for this service driver.
     *
     * @param  array  $options  Options passed to Route::mfa() method.
     * @return void
     */
    public function registerRoutes(Router $router, array $options);
}
