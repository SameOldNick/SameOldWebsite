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
use Illuminate\Support\Facades\Storage;

class ModeratorsFileFactory implements ModeratorsFactory {
    public function __construct(
        protected readonly Container $container,
    )
    {

    }

    /**
     * Builds moderators from config
     *
     * @return Moderator[]
     */
    public function build(): array {
        try {
            $config = $this->getOptionsFromFile();

            if (is_null($config))
                throw new Exception('Unable to read config file.');

            return ModeratorsConfigFactory::buildFromOptions($config)->build();
        } catch (Exception $ex) {
            throw new CannotBuildModeratorsException($ex->getMessage());
        }
    }

    protected function getOptions(): array {
        return (array) $this->container->config->get('moderators.builders.file.options', []);
    }

    protected function getOptionsFromFile(): ?array {
        $options = $this->getOptions();

        return Storage::disk($options['disk'])->json($options['path']);
    }
}
