<?php

namespace App\Components\Backup\Providers;

use App\Components\Backup\Contracts\BackupConfigurationProvider;
use App\Models\FilesystemConfiguration;
use Illuminate\Support\Facades\Log;

class BackupDatabaseConfigurationProvider extends DatabaseConfigurationProvider implements BackupConfigurationProvider
{
    /**
     * {@inheritDoc}
     */
    public function getDisks(): array
    {
        try {
            return FilesystemConfiguration::where('is_active', true)->get()->map(
                fn (FilesystemConfiguration $config) => $config->driver_name
            )->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to fetch active filesystem configurations for backup disks: '.$e->getMessage());

            return [];
        }

    }
}
