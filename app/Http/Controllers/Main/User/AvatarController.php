<?php

namespace App\Http\Controllers\Main\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Controllers\RespondsWithUsersAvatar;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AvatarController extends Controller
{
    use RespondsWithUsersAvatar;

    /**
     * Displays users profile
     *
     * @return Response
     */
    public function view(Request $request, User $user)
    {
        $request->validate([
            'size' => 'sometimes|numeric|min:1',
        ]);

        return $this->respondWithAvatar($user, $request->input('size'));
    }
}
