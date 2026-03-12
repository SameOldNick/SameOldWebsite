<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Controllers\HasPage;
use App\Traits\Controllers\RespondsWithUsersAvatar;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use HasPage;
    use RespondsWithUsersAvatar;

    /**
     * Show the homepage.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('main.home', [
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * Gets avatar of main user.
     */
    public function avatar(Request $request)
    {
        $user = User::find(config('pages.homepage.user', 1));

        return $this->respondWithAvatar($user, $request->input('size'));
    }

    /**
     * {@inheritDoc}
     */
    protected function getPageKey()
    {
        return 'homepage';
    }
}
