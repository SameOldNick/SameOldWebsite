<?php

namespace App\Components\Moderator\Factories;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Exceptions\CannotBuildModeratorsException;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Components\Settings\Facades\PageSettings;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\CommentFlag;
use App\Models\Page;
use Closure;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Components\Moderator\Moderators;

class ModeratorsDatabaseFactory implements ModeratorsFactory {
    public function __construct(
        protected readonly Container $container,
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function build(): array {
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

    protected function getOptions(): array {
        return (array) $this->container->config->get('moderators.builders.database.options', []);
    }

    protected function getExistingOptions(): array {
        return (array) $this->container->config->get('moderators.builders.config.options', []);
    }

    protected function getEnabledModerators(): array {
        $enabled = PageSettings::page('blog')->setting('moderators');

        return !is_null($enabled) ? $enabled : $this->getOptions()['fallback'];
    }

    protected function getMappedClasses(): array {
        return [
            'profanity' => Moderators\ProfanityModerator::class,
            'email' => Moderators\EmailModerator::class,
            'language' => Moderators\LanguageModerator::class,
            'link' => Moderators\LinkModerator::class
        ];
    }
}
