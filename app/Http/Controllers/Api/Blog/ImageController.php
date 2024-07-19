<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        $file->user()->associate($request->user());

        $image->save();

        $image->file()->save($file);

        return $image->load('file');
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {

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
