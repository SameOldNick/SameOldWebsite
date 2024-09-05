<?php

namespace App\Components\Moderator\Exceptions;

use Exception;

/**
 * @template TFlag
 */
abstract class FlagException extends Exception
{
    /**
     * Transforms exception to flag
     *
     * @return TFlag
     */
    abstract public function transformToFlag();
}
