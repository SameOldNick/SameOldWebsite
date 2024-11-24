<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\SecretStore;
use App\Components\MFA\Http\Controllers\OTP\BackupController;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\HashbasedFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

class BackupDriver extends AuthDriver
{
    const DEFAULT_BACKUP_CODE_COUNT = 6;

    public function __construct(
        protected readonly array $config,
        SecretStore $secretStore,
    ) {
        parent::__construct(new HashbasedFactory, $secretStore);
    }

    /**
     * Gets backup codes
     *
     * @return array Array of backup codes
     */
    public function getCodes(string $secret)
    {
        $codes = [];

        $otp = $this->createOtp(OneTimeAuthenticatable::string($secret));

        for ($i = 0; $i < $this->getBackupCodeCount(); $i++) {
            array_push($codes, $otp->at($i));
        }

        return $codes;
    }

    /**
     * {@inheritDoc}
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool
    {
        $oneTimeAuthenticatable = $this->createOneTimeAuthenticatable($authenticatable);

        return $this->createOtp($oneTimeAuthenticatable)->verify($code, null, $this->getBackupCodeCount());
    }

    /**
     * {@inheritDoc}
     */
    public function registerRoutes(Router $router, array $options)
    {
        $router->middleware($this->getMiddleware($options))->group(function () use ($router) {
            $router->get('/auth/mfa/backup', [BackupController::class, 'showBackupCodePrompt'])->name('auth.mfa.backup');
            $router->post('/auth/mfa/backup', [BackupController::class, 'verifyBackupCode'])->name('auth.mfa.backup.verify');
        });
    }

    /**
     * Gets how many backup codes to create and allow.
     */
    public function getBackupCodeCount(): int
    {
        return Arr::get($this->config, 'codes', static::DEFAULT_BACKUP_CODE_COUNT);
    }
}
