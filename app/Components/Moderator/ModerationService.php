<?php

namespace App\Components\Moderator;

use App\Components\Moderator\Contracts\Moderatable;
use App\Components\Moderator\Exceptions\FlagException;

class ModerationService
{
    /**
     * Initializes moderation service
     */
    public function __construct() {}

    /**
     * Moderates a model
     *
     * @param  Moderatable  $moderatable  Model to moderate
     * @return TFlag[] Array of flags
     *
     * @template TFlag The flag type
     */
    public function moderate(Moderatable $moderatable)
    {
        $flags = [];

        foreach ($moderatable->getModerators() as $moderator) {
            if (! $moderator->isEnabled()) {
                continue;
            }

            try {
                $moderator->moderate($moderatable);
            } catch (FlagException $ex) {
                array_push($flags, $ex->transformToFlag());
            }
        }

        return $flags;
    }
}
