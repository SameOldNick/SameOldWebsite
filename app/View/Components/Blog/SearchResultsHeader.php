<?php

namespace App\View\Components\Blog;

use App\Http\Requests\BlogSearchRequest;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchResultsHeader extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly BlogSearchRequest $request,
    ) {}

    /**
     * Gets link for sorting by relevance
     *
     * @return string
     */
    public function sortByRelevanceLink(): string
    {
        return $this->request->fullUrlWithQuery([
            'sort' => 'relevance',
            'order' => $this->request->isSortBy('relevance') && $this->request->isOrderAscending() ? 'desc' : 'asc'
        ]);
    }

    /**
     * Gets link for sorting by date
     *
     * @return string
     */
    public function sortByDateLink(): string
    {
        return $this->request->fullUrlWithQuery([
            'sort' => 'date',
            'order' => $this->request->isSortBy('date') && $this->request->order() === 'desc' ? 'asc' : 'desc'
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.main.blog.search-results-header');
    }
}
