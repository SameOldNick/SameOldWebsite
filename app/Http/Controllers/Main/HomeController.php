<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Controllers\HasPage;
use App\Traits\Controllers\RespondsWithUsersAvatar;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use RespondsWithUsersAvatar;
    use HasPage;

    /**
     * Show the homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('main.home', [
            'settings' => $this->getSettingsCached(),
        ]);
    }

    public function avatar(Request $request)
    {
        $user = User::find(config('pages.homepage.user', 1));

        return $this->respondWithAvatar($user, $request->input('size'));
    }

    /**
     * @inheritDoc
     */
    protected function getPageKey() {
        return 'homepage';
    }

    /**
     * Gets cached copy of Page Settings.
     *
     * @return \App\Components\Settings\PageSettings
     */
    protected function getSettingsCached()
    {
        return $this->getSettings()->driver('cache');
    }
}
