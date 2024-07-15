<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Revision;
use Illuminate\Http\Request;

class BlogArticleController extends Controller
{
    /**
     * Display the specified article.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function single(Request $request, Article $article)
    {
        return view('main.blog.single', compact('article'));
    }

    /**
     * Displays the specified revision for article
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function singleRevision(Request $request, Article $article, Revision $revision)
    {
        return view('main.blog.single', compact('article', 'revision'));
    }
}
