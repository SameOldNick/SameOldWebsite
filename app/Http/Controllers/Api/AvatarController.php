<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\FileUploadException;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AvatarController extends Controller
{
    /**
     * Gets the URL to the current avatar for the user.
     *
     * @param Request $request
     * @return array
     */
    public function avatar(Request $request)
    {
        $request->validate([
            'size' => 'sometimes|numeric|min:1'
        ]);

        $url = URL::temporarySignedRoute('api.avatar.download', now()->addMinutes(30), ['user' => $request->user(), 'size' => $request->size]);

        return compact('url');
    }

    /**
     * Download the current avatar for the user.
     *
     * @param Request $request
     * @return array
     */
    public function downloadAvatar(Request $request, User $user)
    {
        $request->validate([
            'size' => 'sometimes|numeric|min:1'
        ]);

        if (!is_null($user->avatar)) {
            return Storage::download($user->avatar);
        } else {
            $url = sprintf('https://www.gravatar.com/avatar/%s', md5(strtolower($user->email)));

            if ($request->has('size'))
                $url .= sprintf('?s=%d', $request->size);

            return redirect()->away($url);
        }
    }

    /**
     * Uploads a new avatar for the user.
     *
     * @param Request $request
     * @return Response
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|file|mimes:jpg,bmp,png|max:200',
        ]);

        if (($path = $request->file('avatar')->store('avatars')) === false) {
            throw new FileUploadException('An error occurred uploading the avatar.');
        }

        $user = $request->user();

        if (isset($user->avatar) && $path !== $user->avatar) {
            // Delete existing avatar
            Storage::delete($user->avatar);
        }

        $user->avatar = $path;

        $user->save();

        return Response::withMessage('Avatar image has been updated.');
    }

    /**
     * Deletes the authenticated users avatar.
     *
     * @param Request $request
     * @return array
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if (!isset($user->avatar)) {
            return response()->json(['message' => 'Avatar doesn\'t exist.'], 404);
        }

        // Delete existing avatar
        Storage::delete($user->avatar);

        $user->avatar = null;

        $user->save();

        return Response::withMessage('Avatar has been removed.');
    }
}
