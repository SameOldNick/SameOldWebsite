<?php

namespace App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Adapter;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Driver;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\HashbasedFactory;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimePasscode;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\SecretResolver;
use Illuminate\Support\Traits\ForwardsCalls;
use Intervention\Image\Laravel\Facades\Image;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimeAuthenticatable;
use Illuminate\Support\Arr;

class BackupDriver extends AuthDriver {
    const DEFAULT_BACKUP_CODE_COUNT = 6;

    public function __construct(
        protected readonly array $config
    )
    {
        parent::__construct(new HashbasedFactory);
    }

    /**
     * Gets backup codes
     *
     * @return array Array of backup codes
     */
    public function getCodes(string $secret) {
        $codes = [];

        $otp = $this->createOtp(OneTimeAuthenticatable::string($secret));

        for ($i = 0; $i < $this->getBackupCodeCount(); $i++) {
            array_push($codes, $otp->at($i));
        }

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool
    {
        $oneTimeAuthenticatable = $this->createOneTimeAuthenticatable($authenticatable);

        return $this->createOtp($oneTimeAuthenticatable)->verify($code, null, $this->getBackupCodeCount());
    }

    /**
     * Gets how many backup codes to create and allow.
     *
     * @return integer
     */
    public function getBackupCodeCount(): int {
        return Arr::get($this->config, 'codes', static::DEFAULT_BACKUP_CODE_COUNT);
    }
}
