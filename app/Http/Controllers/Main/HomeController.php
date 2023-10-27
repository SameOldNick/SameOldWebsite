<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Traits\Controllers\RespondsWithUsersAvatar;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    use RespondsWithUsersAvatar;

    /**
     * Show the homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $articles =
            Article::published()
                ->latest('published_at')
                ->limit(5)
                ->get();

        return view('main.home', compact('articles'));
    public function avatar(Request $request) {
        $user = User::find(config('pages.homepage.user', 1));

        return $this->respondWithAvatar($user, $request->input('size'));
    }
    }
}
