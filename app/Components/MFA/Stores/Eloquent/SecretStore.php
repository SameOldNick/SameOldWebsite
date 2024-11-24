<?php

namespace App\Components\MFA\Stores\Eloquent;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\SecretStore as StoreContract;

class SecretStore implements StoreContract
{
    /**
     * @inheritDoc
     */
    public function storeSecrets(MultiAuthenticatable $authenticatable, string $authSecret, string $backupSecret): void
    {
        $authenticatable->oneTimePasscodeSecrets()->create([
            'auth_secret' => $authSecret,
            'backup_secret' => $backupSecret,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function removeSecrets(MultiAuthenticatable $authenticatable): void
    {
        $authenticatable->oneTimePasscodeSecrets()->delete();
    }

    /**
     * @inheritDoc
     */
    public function getAuthSecret(MultiAuthenticatable $authenticatable): ?string
    {
        return optional($authenticatable->oneTimePasscodeSecrets)->auth_secret;
    }

    /**
     * @inheritDoc
     */
    public function getBackupSecret(MultiAuthenticatable $authenticatable): ?string
    {
        return optional($authenticatable->oneTimePasscodeSecrets)->backup_secret;
    }
}
