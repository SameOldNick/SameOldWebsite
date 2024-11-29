<?php

namespace App\Components\MFA\Services\Authenticator;

use App\Components\MFA\Contracts\OneTimePasscode\Factory;
use App\Components\MFA\Contracts\SecretStore;
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
        return $this->newAuthDriver(new TimebasedFactory);
    }

    /**
     * Creates Hash-based One Time Passcode driver
     *
     * @return AuthDriver
     */
    protected function createHotpDriver()
    {
        return $this->newAuthDriver(new HashbasedFactory);
    }

    /**
     * Creates backup driver
     *
     * @return BackupDriver
     */
    protected function createBackupDriver()
    {
        return new BackupDriver(
            $this->getConfig('backup'),
            $this->getContainer()->make(SecretStore::class),
        );
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

    /**
     * Creates new AuthDriver with factory
     */
    protected function newAuthDriver(Factory $factory): AuthDriver
    {
        return new AuthDriver(
            $factory,
            $this->getContainer()->make(SecretStore::class),
        );
    }
}
