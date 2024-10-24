<?php

namespace App\Components\Backup\Providers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

abstract class DatabaseConfigurationProvider
{
    /**
     * The table storing the configuration.
     */
    protected string $table = 'backup_config';

    /**
     * Gets table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Checks if value for key exists in database
     *
     * @param string $key
     * @return boolean
     */
    protected function hasValue(string $key): bool
    {
        try {
            return DB::table($this->getTable())->where('key', $key)->count() > 0;
        } catch (QueryException) {
            // In case unable to connect to database (possibly because app isn't setup yet)
            return false;
        }
    }

    /**
     * Gets array value from table
     */
    protected function getArrayValue(string $key, array $default = []): array
    {
        $row = DB::table($this->getTable())->where('key', $key)->first();

        return $row ? explode(';', $row->value) : $default;
    }

    /**
     * Gets string value or default
     *
     * @param  mixed  $default
     * @return mixed
     */
    protected function getStringValue(string $key, $default = null)
    {
        $row = DB::table($this->getTable())->where('key', $key)->first();

        return $row ? $row->value : $default;
    }
}
