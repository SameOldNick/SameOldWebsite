<?php

namespace App\Models\Presenters;

use App\Models\Comment;
use Spatie\Url\Url as SpatieUrl;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class CommentPresenter extends Presenter
{
    public function __construct(
        protected readonly Comment $comment
    ) {}

    /**
     * Gets the ID to use for a HTML element.
     *
     * @return string
     */
    public function elementId(): string
    {
        $prefix = class_basename($this->comment);
        $suffix = $this->comment->getKey();

        return sprintf('%s-%s', Str::kebab($prefix), $suffix);
    }

    /**
     * Gets the public or private URL
     *
     * @return string
     */
    public function url(): string
    {
        return Gate::allows('view', $this->comment) ? $this->publicUrl() : $this->privateUrl();
    }

    /**
     * Creates public link to this comment.
     *
     * @return string
     */
    public function publicUrl(bool $absolute = true)
    {
        $params = ['comment' => $this->comment];
        $fragment = $this->elementId();

        $url = SpatieUrl::fromString($this->comment->article->presenter()->publicUrl($absolute, $params))->withFragment($fragment);

        return (string) $url;
    }

    /**
     * Creates temporary signed URL to this comment.
     *
     * @param  int  $minutes  Minutes until URL expires (default: 30)
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @return string
     */
    public function privateUrl(int $minutes = 30, bool $absolute = true)
    {
        $params = ['comment' => $this->comment];
        $fragment = $this->elementId();

        $url = SpatieUrl::fromString($this->comment->article->presenter()->privateUrl($minutes, $absolute, $params))->withFragment($fragment);

        return (string) $url;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'url' => $this->url()
        ];
    }
}
