<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\AuthServiceInterface;
use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\OneTimePasscode\Factory;
use App\Components\MFA\Exceptions\MultiAuthNotConfiguredException;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\TimebasedFactory;
use Illuminate\Contracts\Auth\Authenticatable;
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
