<?php

namespace App\Components\MFA\Contracts;

use App\Components\MFA\Contracts\MultiAuthenticatable;

interface SecretStore
{
    /**
     * Stores secrets with multi-authenticatable
     *
     * @param MultiAuthenticatable $authenticatable
     * @param string $authSecret
     * @param string $backupSecret
     * @return void
     */
    public function storeSecrets(MultiAuthenticatable $authenticatable, string $authSecret, string $backupSecret): void;

    /**
     * Removes secrets from multi-authenticatable
     *
     * @param MultiAuthenticatable $authenticatable
     * @return void
     */
    public function removeSecrets(MultiAuthenticatable $authenticatable): void;

    /**
     * Gets auth secret for multi-authenticatable
     *
     * @param MultiAuthenticatable $authenticatable
     * @return string|null Secret (or null if it doesn't exist)
     */
    public function getAuthSecret(MultiAuthenticatable $authenticatable): ?string;

    /**
     * Gets backup secret for multi-authenticatable
     *
     * @param MultiAuthenticatable $authenticatable
     * @return string|null Secret (or null if it doesn't exist)
     */
    public function getBackupSecret(MultiAuthenticatable $authenticatable): ?string;
}
