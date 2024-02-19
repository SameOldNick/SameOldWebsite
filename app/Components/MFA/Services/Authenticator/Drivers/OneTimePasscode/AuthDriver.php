<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\AuthServiceInterface;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\OneTimePasscode\Factory;
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
        protected readonly Factory $factory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function isConfigured(MultiAuthenticatable $authenticatable): bool
    {
        try {
            $secret = $this->createOneTimeAuthenticatable($authenticatable)->resolveSecret();

            return ! is_null($secret);
        } catch (MultiAuthNotConfiguredException $ex) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool
    {
        $oneTimeAuthenticatable = $this->createOneTimeAuthenticatable($authenticatable);

        return $this->createOtp($oneTimeAuthenticatable)->verify($code);
    }

    /**
     * @inheritDoc
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
     * @param MultiAuthenticatable $authenticatable
     * @param string $secret
     * @return SetupConfiguration
     */
    public function setup(MultiAuthenticatable $authenticatable, string $secret)
    {
        $otp = (new TimebasedFactory())->createForAuthenticatable($secret, $authenticatable);

        return new SetupConfiguration($otp);
    }

    /**
     * Installs MFA for authenticatable.
     *
     * @param MultiAuthenticatable $authenticatable
     * @param string $authSecret
     * @param string $backupSecret
     * @return mixed
     */
    public function install(MultiAuthenticatable $authenticatable, string $authSecret, string $backupSecret)
    {
        return $authenticatable->oneTimePasscodeSecrets()->create([
            'auth_secret' => $authSecret,
            'backup_secret' => $backupSecret,
        ]);
    }

    /**
     * Uninstalls MFA from authenticatable.
     *
     * @param Authenticatable $authenticatable
     * @return mixed
     */
    public function uninstall(Authenticatable $authenticatable)
    {
        return $authenticatable->oneTimePasscodeSecrets()->delete();
    }

    /**
     * Gets the middleware for the routes.
     *
     * @param array $options
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
     *
     * @param OneTimeAuthenticatable $authenticatable
     * @return OTPInterface
     */
    protected function createOtp(OneTimeAuthenticatable $authenticatable): OTPInterface
    {
        return $this->factory->create($authenticatable->resolveSecret());
    }

    /**
     * Creates secret resolver for authenticatable.
     *
     * @param MultiAuthenticatable $authenticatable
     * @return OneTimeAuthenticatable
     */
    protected function createOneTimeAuthenticatable(MultiAuthenticatable $authenticatable): OneTimeAuthenticatable
    {
        if ($authenticatable instanceof OneTimeAuthenticatable) {
            return $authenticatable;
        }

        return OneTimeAuthenticatable::auth($authenticatable);
    }
}