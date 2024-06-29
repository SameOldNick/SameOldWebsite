<?php

namespace App\Components\Moderator\Factories;

use App\Components\Moderator\Contracts\Moderator;
use App\Components\Moderator\Contracts\ModeratorsFactory;
use App\Components\Moderator\Exceptions\FlagCommentException;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\CommentFlag;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

class ModeratorsConfigFactory implements ModeratorsFactory {
    protected readonly array $options;

    public function __construct(
        protected readonly Container $container,
        ?array $options = null,
    )
    {
        $this->options = $options ?? $this->getDefaultOptions();
    }

    /**
     * @inheritDoc
     */
    public function build(): array {
        $moderators = [];

        foreach ($this->options['moderators'] as $item) {
            $class = $item['moderator'];

            if (!is_subclass_of($class, Moderator::class))
                continue;

            /**
             * @var Moderator $moderator
             */
            $moderator = $this->container->make($class, ['config' => $item]);

            array_push($moderators, $moderator);
        }

        return $moderators;
    }

    protected function getDefaultOptions(): array {
        return (array) $this->container->config->get('moderators.builders.config.options', []);
    }

    public static function buildFromOptions(array $options): self {
        return app(self::class, ['options' => $options]);
    }
}
