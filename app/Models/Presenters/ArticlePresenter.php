<?php

namespace App\Models\Presenters;

use App\Models\Article;
use Illuminate\Support\Facades\URL;

class ArticlePresenter extends Presenter
{
    public function __construct(
        protected readonly Article $article
    ) {}

    /**
     * Gets the article URL
     *
     * @return string
     */
    public function url(): string
    {
        return $this->article->is_published ? $this->publicUrl() : $this->privateUrl();
    }

    /**
     * Creates public link to this article
     *
     * @param  array  $params  Any extra parameters to include in URL
     * @return string
     */
    public function publicUrl(bool $absolute = true, array $params = []): string
    {
        return URL::route('blog.single', [...$params, 'article' => $this->article], $absolute);
    }

    /**
     * Creates temporary signed URL to this article
     *
     * @param  int  $minutes  Minutes until URL expires (default: 30)
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @param  array  $params  Any extra parameters to include in URL
     * @return string
     */
    public function privateUrl(int $minutes = 30, bool $absolute = true, array $params = []): string
    {
        return URL::temporarySignedRoute('blog.preview', $minutes * 60, [...$params, 'article' => $this->article], $absolute);
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
