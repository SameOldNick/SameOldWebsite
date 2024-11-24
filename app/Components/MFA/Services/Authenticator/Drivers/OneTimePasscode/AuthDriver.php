<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\AuthServiceInterface;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\OneTimePasscode\Factory;
use App\Components\MFA\Contracts\SecretStore;
use App\Components\MFA\Contracts\Stores\AuthSecretStore;
use App\Components\MFA\Contracts\Stores\BackupSecretStore;
use App\Components\MFA\Exceptions\MultiAuthNotConfiguredException;
use App\Components\MFA\Http\Controllers\OTP\AuthController;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\TimebasedFactory;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use OTPHP\OTP;
use OTPHP\OTPInterface;

class AuthDriver implements AuthServiceInterface
{
    public function __construct(
        protected readonly Factory $factory,
        protected readonly SecretStore $secretStore,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function isConfigured(MultiAuthenticatable $authenticatable): bool
    {
        try {
            $secret = $this->createOneTimeAuthenticatable($authenticatable)->resolveSecret();

            return ! empty($secret);
        } catch (MultiAuthNotConfiguredException $ex) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool
    {
        $oneTimeAuthenticatable = $this->createOneTimeAuthenticatable($authenticatable);

        return $this->createOtp($oneTimeAuthenticatable)->verify($code);
    }

    /**
     * {@inheritDoc}
     */
    public function registerRoutes(Router $router, array $options)
    {
        $router->middleware($this->getMiddleware($options))->group(function () use ($router) {
            $router->get('/auth/mfa', [AuthController::class, 'showMFAPrompt'])->name('auth.mfa');
            $router->post('/auth/mfa', [AuthController::class, 'verifyMFACode'])->name('auth.mfa.verify');
        });
    }

    /**
     * Gets setup configuration.
     *
     * @return SetupConfiguration
     */
    public function setup(MultiAuthenticatable $authenticatable, string $secret)
    {
        $otp = (new TimebasedFactory)->createForAuthenticatable($secret, $authenticatable);

        return new SetupConfiguration($otp);
    }

    /**
     * Installs MFA for authenticatable.
     *
     * @return mixed
     */
    public function install(MultiAuthenticatable $authenticatable, string $authSecret, string $backupSecret)
    {
        $this->secretStore->storeSecrets($authenticatable, $authSecret, $backupSecret);
    }

    /**
     * Uninstalls MFA from authenticatable.
     *
     * @return mixed
     */
    public function uninstall(MultiAuthenticatable $authenticatable)
    {
        $this->secretStore->removeSecrets($authenticatable);
    }

    /**
     * Gets the middleware for the routes.
     *
     * @return array
     */
    protected function getMiddleware(array $options)
    {
        $middleware = [];

        if (Arr::get($options, 'otp.redirect_if_authenticated.enabled', false)) {
            array_push($middleware, sprintf('%s:%s', RedirectIfAuthenticated::class, Arr::get($options, 'otp.redirect_if_authenticated.guard', null)));
        }

        if (Arr::get($options, 'otp.throttle.enabled', false)) {
            array_push($middleware, ThrottleRequests::with(Arr::get($options, 'otp.throttle.max_attempts'), Arr::get($options, 'otp.throttle.decay_minutes'), Arr::get($options, 'otp.throttle.prefix')));
        }

        return $middleware;
    }

    /**
     * Creates OTP instance.
     */
    protected function createOtp(OneTimeAuthenticatable $authenticatable): OTPInterface
    {
        return $this->factory->create($authenticatable->resolveSecret());
    }

    /**
     * Creates secret resolver for authenticatable.
     */
    protected function createOneTimeAuthenticatable(MultiAuthenticatable $authenticatable): OneTimeAuthenticatable
    {
        if ($authenticatable instanceof OneTimeAuthenticatable) {
            return $authenticatable;
        }

        $secret = $this->secretStore->getAuthSecret($authenticatable);

        return OneTimeAuthenticatable::string($secret ?? MultiAuthNotConfiguredException::throw());
    }
}
