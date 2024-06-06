<?php

namespace App\Components\Macros;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Schema;

class BlueprintMixin
{
    public function dropColumnSafe()
    {
        /**
         * SQLite doesn't support dropping columns.
         * Source: https://stackoverflow.com/a/21019278/533242
         */
        return function (...$args) {
            /**
             * @var Blueprint $this
             */
            if (Schema::getConnection() instanceof SQLiteConnection) {
                // Do nothing
                /** @see Blueprint::ensureCommandsAreValid */
            } else {
                $this->dropColumn(...$args);
            }
        };
    }

    public function dropForeignSafe()
    {
        /**
         * SQLite doesn't support dropping foreign keys.
         * Source: https://github.com/laravel/framework/issues/23461
         */
        return function ($args) {
            /**
             * @var Blueprint $this
             */
            if (Schema::getConnection() instanceof SQLiteConnection) {
                // Do nothing
                /** @see Blueprint::ensureCommandsAreValid */
            } else {
                $this->dropForeign($args);
            }
        };
    }
}
