<?php

namespace App\Components\Macros;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder as Schema;
use Illuminate\Support\Facades\DB;

class SchemaMixin
{
    public function rebuildTableSqlite()
    {
        /**
         * SQLite doesn't support dropping columns.
         * Rebuilds images table since SQLite doesn't support removing columns
         * Adding rows using the existing table schema will cause errors.
         * Source: https://stackoverflow.com/a/21019278/533242
         */
        return function ($table, array $without = [], array $withoutIndexes = []) {
            /**
             * @var Schema $this
             */

            // Get the existing schema and filter out the columns to be dropped
            $structure = array_filter($this->getColumns($table), function ($col) use ($without) {
                return ! in_array($col['name'], $without);
            });

            // Get column names
            $columns = array_column($structure, 'name');

            // Get the table's current indexes
            // $indexes = DB::select(DB::raw("PRAGMA index_list($table)"));
            $indexes = array_filter($this->getIndexes($table), function ($index) use ($withoutIndexes) {
                return ! in_array($index['name'], $withoutIndexes);
            });

            // Get the table's foreign keys
            $foreignKeys = array_filter($this->getForeignKeys($table), function ($foreignKey) use ($without) {
                return count(array_intersect($foreignKey['columns'], $without)) === 0;
            });

            // Create a temporary table with the new structure
            $this->create("{$table}_temp", function (Blueprint $newTable) use ($structure, $indexes, $foreignKeys) {
                $paramMappings = [
                    'nullable' => 'nullable',
                    'default' => 'default',
                    'collation' => 'collation',
                ];

                /**
                 * Setting column as auto increment in SQLite causes
                 * it to also be set as primary key. If a primary key
                 * index also exists, it will cause an error. Therefore,
                 * only one column can be set as auto increment or an index
                 * can be set as primary.
                 */
                $hasAutoIncrement = false;
                $primaryKey = null;

                foreach ($structure as $col) {
                    // Add each column to the new table
                    ['name' => $name, 'type' => $type] = $col;

                    if (strpos($type, 'varchar') !== false) {
                        $type = str_replace('varchar', 'string', $type);
                    }

                    $params = [];

                    foreach ((array) $col as $key => $value) {
                        if (isset($paramMappings[$key])) {
                            $params[$paramMappings[$key]] = $value;
                        } elseif ($key === 'generation' && ! is_null($value)) {
                            $paramKey = match ($value['type']) {
                                'virtual' => 'virtualAs',
                                'stored' => 'storedAs',
                                default => null
                            };

                            if (! is_null($paramKey)) {
                                $params[$paramKey] = $value['expression'];
                            }
                        } elseif ($key === 'auto_increment') {
                            $params['autoIncrement'] = $value;

                            if ($value) {
                                $hasAutoIncrement = true;
                                $primaryKey = $name;
                            }
                        }
                    }

                    $newTable->addColumn($type, $name, $params);
                }

                foreach ($indexes as $key => $index) {
                    // Creating an explicit primary key will cause a conflict
                    // As mentioned above, columns that are integers and auto increment are set as the primary key.
                    if ($primaryKey && in_array($primaryKey, $index['columns'])) {
                        continue;
                    }

                    if ($index['primary'] && ! $hasAutoIncrement) {
                        $newTable->primary($index['columns']);
                    } elseif ($index['unique']) {
                        $newTable->unique($index['columns']);
                    } else {
                        $newTable->index($index['columns']);
                    }
                }

                foreach ($foreignKeys as $foreignKey) {
                    $newTable->foreign($foreignKey['columns'])
                        ->on($foreignKey['foreign_table'])
                        ->references($foreignKey['foreign_columns'])
                        ->onUpdate($foreignKey['on_update'])
                        ->onDelete($foreignKey['on_delete']);
                }
            });

            // Copy the data from the old table to the new table
            DB::table("{$table}_temp")->insertUsing($columns, DB::table($table)->select($columns));

            // Drop the old table
            $this->drop($table);

            // Rename the new table to the old table name
            $this->rename("{$table}_temp", $table);
        };
    }
}
