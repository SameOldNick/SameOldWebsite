<?php

namespace App\Components\Moderator\Contracts;

interface Moderatable
{
    /**
     * Moderators to run through
     *
     * @return Moderator[]
     */
    public function getModerators(): array;
}
