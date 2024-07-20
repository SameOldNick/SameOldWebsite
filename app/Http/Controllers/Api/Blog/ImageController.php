<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\File;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Image::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user()->roles->containsAll(['manage_images']) ? Image::all() : Image::owned($request->user())->get();
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return $image;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImageRequest $request)
    {
        $request->validate([
            'image' => 'required|image',
            'description' => 'nullable|string|max:255',
        ]);

        $image = new Image([
            'description' => $request->has('description') ? $request->description : null,
        ]);

        $path = $request->file('image')->store('images');
        $file = File::createFromFilePath($path, null, true);

        $file->user()->associate($request->filled('user') && $request->hasRoles(['manage_images']) ? User::find($request->user) : $request->user());

        $image->save();

        $image->file()->save($file);

        return $image->load('file');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        if ($request->has('description')) {
            $image->description = $request->description;
        }

        if ($request->filled('user') && $request->hasRoles(['manage_images'])) {
            $image->file->user()->associate(User::find($request->user));
        }

        $image->push();

        return $image;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        $this->authorize($image);

        $image->delete();

        return [
            'success' => __('Image ":name" was removed.', ['name' => $image->file->name]),
        ];
    }
}
