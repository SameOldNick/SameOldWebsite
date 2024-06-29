<?php

namespace App\Components\Moderator\Factories;

use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Moderators;
use App\Components\Settings\Facades\PageSettings;
use Illuminate\Contracts\Container\Container;

class ModeratorsDatabaseFactory implements ModeratorsFactory
{
    public function __construct(
        protected readonly Container $container,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function build(): array
    {
        $mapped = $this->getMappedClasses();

        $enabled = $this->getEnabledModerators();
        $options = $this->getExistingOptions();

        $options['moderators'] = array_map(function ($moderator) use ($mapped, $enabled) {
            $class = $moderator['moderator'];

            if ($key = array_search($class, $mapped)) {
                $moderator['enabled'] = in_array($key, $enabled);
            }

            return $moderator;
        }, $options['moderators']);

        return ModeratorsConfigFactory::buildFromOptions($options)->build();

    }

    /**
     * Gets database options
     */
    protected function getOptions(): array
    {
        return (array) $this->container->config->get('moderators.builders.database.options', []);
    }

    /**
     * Gets existing config options
     */
    protected function getExistingOptions(): array
    {
        return (array) $this->container->config->get('moderators.builders.config.options', []);
    }

    /**
     * Gets enabled moderators
     *
     * @return list<string>
     */
    protected function getEnabledModerators(): array
    {
        $enabled = PageSettings::page('blog')->setting('moderators');

        return ! is_null($enabled) ? $enabled : $this->getOptions()['fallback'];
    }

    /**
     * Gets keys for moderators
     *
     * @return array<string, class-string>
     */
    protected function getMappedClasses(): array
    {
        return [
            'profanity' => Moderators\ProfanityModerator::class,
            'email' => Moderators\EmailModerator::class,
            'language' => Moderators\LanguageModerator::class,
            'link' => Moderators\LinkModerator::class,
        ];
    }
}
