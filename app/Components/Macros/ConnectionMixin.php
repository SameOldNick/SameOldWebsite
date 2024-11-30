<?php

namespace App\Components\Macros;

use Exception;
use Illuminate\Database\Connection;

class ConnectionMixin
{
    public function isConnected()
    {
        return function () {
            /**
             * @var Connection $this
             */
            try {
                // Throws exception if not connected
                // Source: https://stackoverflow.com/a/40778219/533242
                $pdo = $this->getPdo();

                return ! is_null($pdo);
            } catch (Exception $ex) {
                return false;
            }
        };
    }
}
