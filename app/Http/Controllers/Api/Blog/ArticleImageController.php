<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Image;
use App\Policies\ArticleImagePolicy;
use Illuminate\Http\Request;

class ArticleImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Article $article)
    {
        $this->authorize($article);

        return $article->images;
    }

    /**
     * Attaches image to article.
     */
    public function attach(Article $article, Image $image)
    {
        $this->authorize([$article, $image]);

        $article->images()->attach($image);

        return $article->images;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function detach(Article $article, Image $image)
    {
        $this->authorize([$article, $image]);

        // TODO: Don't allow if set as main image.

        $article->images()->detach($image);

        return $article->images;
    }

    /**
     * Sets main image for article.
     */
    public function mainImage(Request $request, Article $article, Image $image)
    {
        $this->authorize([$article, $image]);

        $article->mainImage()->associate($image);
        $article->save();

        return $article;
    }

    /**
     * Removes main image for article.
     */
    public function destroyMainImage(Request $request, Article $article)
    {
        $this->authorize([$article]);

        $article->mainImage()->dissociate();
        $article->save();

        return $article;
    }
}
