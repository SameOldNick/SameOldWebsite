<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('index', Image::class);

        return $request->user()->hasRoles(['admin']) ? Image::all() : Image::owned($request->user())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Image::class);

        $request->validate([
            'image' => 'required|image',
            'description' => 'nullable|string|max:255',
        ]);

        $image = new Image([
            'description' => isset($request->description) ? $request->description : null,
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
        $this->authorize('view', $image);

        return $image;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        $this->authorize('delete', $image);

        $image->delete();

        return [
            'success' => __('Image ":name" was removed.', ['name' => $image->file->name]),
        ];
    }
}
