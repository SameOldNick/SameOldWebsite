<?php

namespace App\Components\Backup\DbDumper;

use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Support\Arr;
use Spatie\DbDumper\Compressors;
use Spatie\DbDumper\Databases\MySql;

class MySqlPHP extends MySql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function dumpToFile(string $dumpFile): void
    {
        $dump = $this->createMysqlDump();

        $dump->start($dumpFile);
    }

    /**
     * Creates Mysqldump instance (generates SQL dump using PHP)
     */
    protected function createMysqlDump(): Mysqldump
    {
        // TODO: Move to seperate factory class.
        return new Mysqldump($this->createMysqlDumpDsn(), $this->userName, $this->password, $this->createMysqlDumpSettings());
    }

    /**
     * Gets DSN string for the Mysqldump instance.
     */
    protected function createMysqlDumpDsn(): string
    {
        $options = [];

        if (! empty($this->host)) {
            $options['host'] = $this->host;
        }

        if (! empty($this->socket)) {
            $options['unix_socket'] = $this->socket;
        }

        $options['dbname'] = $this->dbName;

        return sprintf('mysql:%s', implode(';', Arr::map($options, fn ($value, $key) => sprintf('%s=%s', $key, $value))));
    }

    /**
     * Gets the settings for the Mysqldump instance.
     */
    protected function createMysqlDumpSettings(): array
    {
        return [
            'include-tables' => $this->includeTables,
            'exclude-tables' => $this->excludeTables,
            'compress' => $this->getCompress(),
            'no-data' => ! $this->includeData,
            'reset-auto-increment' => $this->skipAutoIncrement,
            'add-locks' => true, // If true, This writes 'LOCK TABLE' to the file
            'databases' => true, // Needs to be to set in order for 'CREATE DATABASE' to be included.
            'default-character-set' => $this->defaultCharacterSet ?: Mysqldump::UTF8,
            'extended-insert' => $this->useExtendedInserts,
            'no-create-info' => ! $this->createTables,
            'lock-tables' => ! $this->skipLockTables, // If true, this locks the table when dumping.
            'single-transaction' => $this->useSingleTransaction,
            'skip-comments' => $this->skipComments,
        ];
    }

    /**
     * Gets the compression algorithm to use.
     */
    protected function getCompress(): string
    {
        return match (true) {
            $this->compressor instanceof Compressors\Bzip2Compressor => Mysqldump::BZIP2,
            $this->compressor instanceof Compressors\GzipCompressor => Mysqldump::GZIP,
            default => Mysqldump::NONE
        };
    }
}
