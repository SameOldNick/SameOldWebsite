<?php

namespace App\Traits\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait RespondsWithUsersAvatar
{
    protected function respondWithAvatar(User $user, ?int $size)
    {
        if ($this->hasUploadedAvatar($user)) {
            return Storage::download($user->avatar);
        } elseif ($response = $this->respondWithOauthProviderAvatar($user, $size)) {
            return $response;
        } else {
            return $this->respondWithDefaultAvatar($user, $size);
        }
    }

    protected function hasUploadedAvatar(User $user)
    {
        return ! is_null($user->avatar);
    }

    protected function respondWithUploadedAvatar(User $user, ?int $size)
    {
        return Storage::download($user->avatar);
    }

    protected function respondWithOauthProviderAvatar(User $user, ?int $size)
    {
        foreach ($user->oauthProviders()->latest()->get() as $provider) {
            if (! empty($provider->avatar_url)) {
                $url = $provider->avatar_url;

                if ($size) {
                    $url = Str::appendQuery($url, ['s' => $size]);
                }

                return redirect()->away($url);
            }
        }

        return null;
    }

    protected function respondWithDefaultAvatar(User $user, ?int $size)
    {
        $url = sprintf('https://www.gravatar.com/avatar/%s', md5(strtolower($user->email)));

        if ($size) {
            $url = Str::appendQuery($url, ['s' => $size]);
        }

        return redirect()->away($url);
    }
}
