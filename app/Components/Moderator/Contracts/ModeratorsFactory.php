<?php

namespace App\Components\Moderator\Contracts;

interface ModeratorsFactory {
    /**
     * Builds list of moderators
     *
     * @return Moderator[]
     */
    public function build(): array;
}
