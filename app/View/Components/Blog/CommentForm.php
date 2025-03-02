<?php

namespace App\View\Components\Blog;

use App\Components\Settings\PageSettingsManager;
use App\Models\Article;
use App\Models\Comment as CommentModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class CommentForm extends Component
{
    /**
     * Comment being replied to
     */
    public readonly ?CommentModel $parent;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly Request $request,
        public readonly PageSettingsManager $settings,
        public readonly Article $article,
        ?CommentModel $parent = null,
    ) {
        // Laravel will try to fill in Comment model, so set it to null if it doesn't exist
        $this->parent = ! is_null($parent) && $parent->exists ? $parent : null;
    }

    /**
     * Gets the comment content
     *
     * @return ?string
     */
    public function content()
    {
        // TODO: Set cookie
        return old('comment', $this->request->cookie("{$this->article->slug}-comment"));
    }

    /**
     * Is captcha required?
     */
    public function requireCaptcha(): bool
    {
        $useCaptcha = $this->settings->page('blog')->setting('use_captcha');

        return ($useCaptcha === 'guest' && ! $this->request->user()) || $useCaptcha === 'all';
    }

    /**
     * Generate an element ID
     */
    public function generateElementId(string $name): string
    {
        return $this->parent ? "{$name}{$this->parent->getKey()}" : $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return ! is_null($this->parent) ?
            view('components.main.blog.comment-form-reply') :
            view('components.main.blog.comment-form');
    }
}
