<?php

namespace App\Components\MFA\Services\Authenticator;

use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\AuthDriver;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\BackupDriver;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\HashbasedFactory;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Factories\TimebasedFactory;
use Illuminate\Support\Manager;

class AuthenticatorService extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'totp';
    }

    /**
     * Creates Time-based One Time Passcode driver
     *
     * @return AuthDriver
     */
    protected function createTotpDriver()
    {
        return new AuthDriver(new TimebasedFactory);
    }

    /**
     * Creates Hash-based One Time Passcode driver
     *
     * @return AuthDriver
     */
    protected function createHotpDriver()
    {
        return new AuthDriver(new HashbasedFactory);
    }

    /**
     * Creates backup driver
     *
     * @return BackupDriver
     */
    protected function createBackupDriver()
    {
        $config = $this->getConfig('backup');

        return new BackupDriver($config);
    }

    /**
     * Gets configuration options for key.
     *
     * @return array
     */
    protected function getConfig(string $key)
    {
        return config("mfa.authenticator.drivers.{$key}", []);
    }
}
