<?php

namespace App\Traits\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

trait RespondsWithUsersAvatar
{
    protected function respondWithAvatar(User $user, ?int $size)
    {
        if (! is_null($user->avatar)) {
            return Storage::download($user->avatar);
        } else {
            return $this->respondWithDefaultAvatar($user, $size);
        }
    }

    protected function respondWithDefaultAvatar(User $user, ?int $size)
    {
        $url = sprintf('https://www.gravatar.com/avatar/%s', md5(strtolower($user->email)));

        if ($size) {
            $url .= sprintf('?s=%d', $size);
        }

        return redirect()->away($url);
    }
}
