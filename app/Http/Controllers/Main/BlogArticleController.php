<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ErrorsToSweetAlert;
use App\Models\Article;
use App\Models\Revision;
use Illuminate\Http\Request;

class BlogArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware([ErrorsToSweetAlert::class]);
    }

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
