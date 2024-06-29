<?php

namespace App\Components\Moderator\Factories;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Exceptions\CannotBuildModeratorsException;
use Illuminate\Contracts\Container\Container;

class ModeratorsFallbackFactory implements ModeratorsFactory
{
    public function __construct(
        protected readonly Container $container,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function build(): array
    {
        $config = $this->getConfigFor('fallback');
        $stack = $config['stack'];

        foreach ($stack as $name) {
            try {
                $moderators = $this->buildFrom($name);

                return $moderators;
            } catch (CannotBuildModeratorsException $ex) {

            }
        }

        return $this->getFallbackModerators();
    }

    /**
     * Gets configuration for builder
     */
    protected function getConfigFor(string $builder): array
    {
        return (array) $this->container->config->get("moderators.builders.{$builder}", []);
    }

    /**
     * Gets moderators for when stack fails to build moderators
     *
     * @return Moderator[]
     */
    protected function getFallbackModerators(): array
    {
        return [];
    }

    /**
     * Build moderators from factory
     *
     * @return Moderator[]
     */
    protected function buildFrom(string $name): array
    {
        $config = $this->getConfigFor($name);

        $class = $config['factory'];
        $factory = $this->container->make($class);

        return $factory->build();
    }
}
