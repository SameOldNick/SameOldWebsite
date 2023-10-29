<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Exceptions\FileUploadException;
use App\Http\Controllers\Pages\HomepageController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AvatarController extends HomepageController
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
            'size' => 'sometimes|numeric|min:1',
        ]);

        $url = URL::temporarySignedRoute('api.avatar.download', now()->addMinutes(30), ['size' => $request->size]);

        return compact('url');
    }

    /**
     * Download the current avatar for the user.
     *
     * @param Request $request
     * @return array
     */
    public function downloadAvatar(Request $request)
    {
        $avatar = $this->getSettings()->setting('avatar');

        if (! is_null($avatar)) {
            return Storage::download($avatar);
        } else {
            $url = config('pages.homepage.avatar', '');

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

        $avatar = $this->getSettings()->setting('avatar');

        if (isset($avatar) && $path !== $avatar) {
            // Delete existing avatar
            Storage::delete($avatar);
        }

        $this->getPage()->metaData()->updateOrCreate(
            ['key' => 'avatar'],
            ['value' => $path]
        );

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
        $avatar = $this->getSettings()->setting('avatar');

        if (! isset($avatar)) {
            return response()->json(['message' => 'Avatar doesn\'t exist.'], 404);
        }

        // Delete existing avatar
        Storage::delete($avatar);

        $this->getPage()->metaData()->where('key', 'avatar')->delete();

        return Response::withMessage('Avatar has been removed.');
    }
}
