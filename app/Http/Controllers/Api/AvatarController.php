<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\FileUploadException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Controllers\RespondsWithUsersAvatar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AvatarController extends Controller
{
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

        if (! isset($user->avatar)) {
            return response()->json(['message' => 'Avatar doesn\'t exist.'], 404);
        }

        // Delete existing avatar
        Storage::delete($user->avatar);

        $user->avatar = null;

        $user->save();

        return Response::withMessage('Avatar has been removed.');
    }
}
