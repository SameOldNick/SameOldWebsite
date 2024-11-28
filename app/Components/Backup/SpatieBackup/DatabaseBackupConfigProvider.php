<?php

namespace App\Components\Backup\SpatieBackup;

use Spatie\Backup\Config\BackupConfig;

class DatabaseBackupConfigProvider extends BackupConfig
{
    public function __construct(protected readonly BackupConfig $original)
    {
        parent::__construct(
            name: $original->name,
            source: $original->source,
            databaseDumpCompressor: $original->databaseDumpCompressor,
            databaseDumpFileTimestampFormat: $original->databaseDumpFileTimestampFormat,
            databaseDumpFilenameBase: $original->databaseDumpFilenameBase,
            databaseDumpFileExtension: $original->databaseDumpFileExtension,
            destination: new DatabaseDestinationConfigProvider($original->destination),
            temporaryDirectory: $original->temporaryDirectory,
            password: $original->password,
            encryption: $original->encryption,
            tries: $original->tries,
            retryDelay: $original->retryDelay,
            monitoredBackups: $original->monitoredBackups,
        );
    }
}
