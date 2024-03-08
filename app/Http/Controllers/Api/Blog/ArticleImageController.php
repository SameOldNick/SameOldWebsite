<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Image;
use App\Models\File;
use Illuminate\Http\Request;

class ArticleImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Article $article)
    {
        $this->authorize('view', $article);

        return $article->images;
    }

    /**
     * Attaches image to article.
     */
    public function attach(Article $article, Image $image)
    {
        $this->authorize('update', $article);
        $this->authorize('update', $image);

        $article->images()->attach($image);

        return $article->images;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function detach(Article $article, Image $image)
    {
        $this->authorize('update', $article);
        $this->authorize('update', $image);
        // TODO: Don't allow if set as main image.

        $article->images()->detach($image);

        return $article->images;
    }

    /**
     * Sets main image for article.
     */
    public function mainImage(Request $request, Article $article, Image $image)
    {
        $this->authorize('update', $article);
        $this->authorize('update', $image);

        $article->mainImage()->associate($image);
        $article->save();

        return $article;
    }

    /**
     * Removes main image for article.
     */
    public function destroyMainImage(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $article->mainImage()->dissociate();
        $article->save();

        return $article;
    }


}
