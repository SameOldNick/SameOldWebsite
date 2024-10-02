<?php

namespace App\Http\Controllers\Main;

use App\Components\Macros\Collection\WeightManager;
use App\Components\Search\QueryParser;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogSearchRequest;
use App\Models\Article;
use App\Traits\Controllers\Trackable;
use Illuminate\Support\Carbon;

class BlogController extends Controller
{
    use Trackable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $articles =
            Article::published()
                ->sortedByPublishDate()
                ->paginate(5)
                ->withQueryString();

        return view('main.blog.main', compact('articles'));
    }

    /**
     * Display articles published in month and year
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function archive($year, $month)
    {
        // Don't use createFromDate because it will set the time to the current time (not 00:00)
        $dateTime = Carbon::create($year, $month);

        $articles =
            Article::published()
                ->whereMonth('published_at', $dateTime->month)
                ->whereYear('published_at', $dateTime->year)
                ->sortedByPublishDate()
                ->paginate(5)
                ->withQueryString();

        return view('main.blog.archive', compact('dateTime', 'articles'));
    }

    /**
     * Displays articles matching search query.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function search(BlogSearchRequest $request, QueryParser $queryParser)
    {
        /**
         * @var \App\Models\Collections\ArticleCollection
         */
        $articles = Article::published()->get();

        $query = $request->filled('q') ? $queryParser->parse((string) $request->str('q')) : null;

        if ($query) {
            if ($query->has('tags')) {
                $articles = $articles->withTags($query->get('tags')->all());
            }

            if ($query->has('keywords')) {
                $articles = $articles->withKeywords($query->get('keywords')->all());
            }
        }

        if ($request->sortBy() === 'relevance') {
            $articles = $articles instanceof WeightManager ? $articles->sortByWeights($request->order()) : $articles;
        } elseif ($request->sortBy() === 'date') {
            $articles = $articles instanceof WeightManager ? $articles->getCollection() : $articles;

            $articles = $request->order() === 'asc' ? $articles->sortBy('published_at') : $articles->sortByDesc('published_at');
        }

        /**
         * @var \Illuminate\Pagination\AbstractPaginator
         */
        $articles = $articles->paginate(5)->withQueryString();

        $this->tracker()->set('search', [
            'query' => (string) $request->str('q'),
            'found' => $articles->count(),
        ]);

        return view('main.blog.search-results', compact('request', 'query', 'articles'));
    }
}
