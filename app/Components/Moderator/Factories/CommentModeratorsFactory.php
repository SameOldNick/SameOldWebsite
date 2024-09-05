<?php

namespace App\Components\Moderator\Factories;

use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Moderators\Comments as Moderators;
use App\Components\Settings\Facades\PageSettings;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class CommentModeratorsFactory implements ModeratorsFactory
{
    public function __construct(
        protected readonly Container $container,
        protected readonly array $options
    ) {}

    /**
     * {@inheritDoc}
     */
    public function build(): array
    {
        $mapped = $this->getMappedClasses();

        $enabled = $this->getEnabledModerators();

        $moderators = array_map(function ($moderator) use ($mapped, $enabled) {
            $class = $moderator['moderator'];

            if ($key = array_search($class, $mapped)) {
                $moderator['enabled'] = in_array($key, $enabled);
            }

            return $moderator;
        }, $this->getModeratorsOptions());

        return ModeratorsConfigFactory::buildFromOptions(['moderators' => $moderators])->build();
    }

    /**
     * Gets fallback moderators
     */
    protected function getFallback(): array
    {
        return Arr::get($this->options, 'fallback', []);
    }

    /**
     * Gets existing config options
     */
    protected function getModeratorsOptions(): array
    {
        return Arr::get($this->options, 'moderators', []);
    }

    /**
     * Gets enabled moderators
     *
     * @return list<string>
     */
    protected function getEnabledModerators(): array
    {
        $enabled = PageSettings::page('blog')->setting('moderators');

        return ! is_null($enabled) ? $enabled : $this->getFallback();
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
