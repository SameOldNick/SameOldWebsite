<?php

namespace App\Components\MFA\Contracts;

interface SecretStore
{
    /**
     * Stores secrets with multi-authenticatable
     */
    public function storeSecrets(MultiAuthenticatable $authenticatable, string $authSecret, string $backupSecret): void;

    /**
     * Removes secrets from multi-authenticatable
     */
    public function removeSecrets(MultiAuthenticatable $authenticatable): void;

    /**
     * Gets auth secret for multi-authenticatable
     *
     * @return string|null Secret (or null if it doesn't exist)
     */
    public function getAuthSecret(MultiAuthenticatable $authenticatable): ?string;

    /**
     * Gets backup secret for multi-authenticatable
     *
     * @return string|null Secret (or null if it doesn't exist)
     */
    public function getBackupSecret(MultiAuthenticatable $authenticatable): ?string;
}
