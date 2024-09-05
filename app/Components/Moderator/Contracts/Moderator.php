<?php

namespace App\Components\Moderator\Contracts;

use App\Components\Moderator\Exceptions\FlagException;

/**
 * @template TModeratable of Moderatable
 */
interface Moderator
{
    /**
     * Determines if moderator is enabled
     */
    public function isEnabled(): bool;

    /**
     * Moderates a moderatable
     *
     * @param  TModeratable  $moderatable
     *
     * @throws FlagException Thrown if should be flagged.
     */
    public function moderate($moderatable): void;
}
