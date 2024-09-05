<?php

namespace App\Components\Moderator\Concerns;

use App\Components\Moderator\Contracts\ModeratorsFactory;
use Exception;
use InvalidArgumentException;

trait CreatesModeratorsFactory
{
    /**
     * Creates moderators factory
     *
     * @param string $key Key in config file
     * @return ModeratorsFactory
     */
    public function createModeratorsFactory(string $key)
    {
        $config = config("moderators.{$key}");

        if (!$config)
            throw new InvalidArgumentException("No configuration options found for 'moderators.{$key}'.");

        $factory = $config['factory'];

        if (!is_a($factory, ModeratorsFactory::class, true))
            throw new Exception("The factory for '{$key}' does not implement ModeratorsFactory.");

        $options = $config['options'] ?? [];

        return app($factory, ['options' => $options]);
    }
}
