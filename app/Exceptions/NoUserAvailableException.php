<?php

namespace App\Exceptions;

use Exception;

class NoUserAvailableException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('No User available to associate with the Post.');
    }
}
