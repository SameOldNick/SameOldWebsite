<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Pages\HomepageController as BaseController;
use App\Models\Article;
use App\Models\User;
use App\Traits\Controllers\RespondsWithUsersAvatar;

use Illuminate\Http\Request;

class HomeController extends BaseController
{
    use RespondsWithUsersAvatar;

    /**
     * Show the homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('main.home', [
            'settings' => $this->getSettings()
        ]);
    }

    public function avatar(Request $request) {
        $user = User::find(config('pages.homepage.user', 1));

        return $this->respondWithAvatar($user, $request->input('size'));
    }

    /**
     * Gets Page Settings.
     *
     * @return PageSettings
     */
    protected function getSettings() {
        return parent::getSettings()->driver('cache');
    }
}
