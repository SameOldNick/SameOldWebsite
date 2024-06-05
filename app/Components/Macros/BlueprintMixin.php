<?php

namespace App\Components\Macros;

use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\Schema\Blueprint;

class BlueprintMixin
{
    public function dropForeignSafe() {
        /**
         * SQLite doesn't support dropping foreign keys.
         * Source: https://github.com/laravel/framework/issues/23461
         */
        return function ($args) {
            /**
             * @var Blueprint $this
             */
            if (app('db.connection') instanceof SQLiteConnection) {
                // Do nothing
                /** @see Blueprint::ensureCommandsAreValid */
            } else {
                $this->dropForeign($args);
            }
        };
    }
}
