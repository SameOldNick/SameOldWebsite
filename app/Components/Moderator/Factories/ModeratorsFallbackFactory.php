<?php

namespace App\Components\Moderator\Factories;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Exceptions\CannotBuildModeratorsException;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\CommentFlag;
use Closure;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ModeratorsFallbackFactory implements ModeratorsFactory {
    public function __construct(
        protected readonly Container $container,
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function build(): array {
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

    protected function getConfigFor(string $builder): array {
        return (array) $this->container->config->get("moderators.builders.{$builder}", []);
    }

    /**
     * Gets moderators for when stack fails to build moderators
     *
     * @return Moderator[]
     */
    protected function getFallbackModerators(): array {
        return [];
    }

    protected function buildFrom(string $name): array {
        $config = $this->getConfigFor($name);

        $class = $config['factory'];
        $factory = $this->container->make($class);

        return $factory->build();
    }
}
