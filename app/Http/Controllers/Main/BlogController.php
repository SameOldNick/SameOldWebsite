<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogSearchRequest;
use App\Models\Article;
use Illuminate\Support\Carbon;

class BlogController extends Controller
{
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
     * @param BlogSearchRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function search(BlogSearchRequest $request)
    {
        $query = $request->parsedSearchedQuery();
        $sortBy = $request->has('sort') && $request->str('sort')->lower()->exactly('date') ? 'date' : 'relevance';
        $order = $request->has('order') && $request->str('order')->lower()->exactly('asc') ? 'asc' : 'desc';

        $articles = Article::published()->get();

        if ($request->filled('q')) {
            if ($query->hasTags()) {
                $articles = $articles->withTags($query->getTags()->all());
            }

            if ($query->hasKeywords()) {
                $articles = $articles->withKeywords($query->getKeywords()->all());
            }

            if ($sortBy === 'relevance') {
                $articles = $articles->sortByWeights($order);
            } else {
                $articles = $order === 'asc' ? $articles->sortBy('published_at') : $articles->sortByDesc('published_at');
            }
        }

        $articles = $articles->paginate(5)->withQueryString();

        return view('main.blog.search-results', compact('request', 'articles', 'sortBy', 'order'));
    }
}
