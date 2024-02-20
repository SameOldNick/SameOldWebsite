<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\File;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Article $article)
    {
        return $article->images;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Article $article)
    {
        $request->validate([
            'image' => 'required|image',
            'description' => 'nullable|string|max:255',
        ]);

        $articleImage = new ArticleImage([
            'description' => isset($request->description) ? $request->description : null,
        ]);

        $path = $request->file('image')->store('images');
        $file = File::createFromFilePath($path, null, true);

        $article->images()->save($articleImage)->file()->save($file);

        return $articleImage->load('file');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article, ArticleImage $image)
    {
        return $image;
    }

    /**
     * Sets main image for article.
     */
    public function mainImage(Request $request, Article $article, ArticleImage $image)
    {
        $article->mainImage()->associate($image);
        $article->save();

        return $article;
    }

    /**
     * Removes main image for article.
     */
    public function destroyMainImage(Request $request, Article $article)
    {
        $article->mainImage()->dissociate();
        $article->save();

        return $article;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article, ArticleImage $image)
    {
        $image->delete();

        return [
            'success' => __('Article image ":name" was removed.', ['name' => $image->file->name]),
        ];
    }
}
