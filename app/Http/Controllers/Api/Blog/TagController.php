<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Article $article)
    {
        return $article->tags;
    }

    /**
     * Attaches tags to article
     */
    public function attach(Request $request, Article $article)
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'required|string|max:255'
        ]);

        $tags = $this->transformTags($request->tags);

        $article->tags()->attach($tags);

        return $article->tags;
    }

    /**
     * Detaches tags from article
     */
    public function detach(Request $request, Article $article)
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'required|string|max:255'
        ]);

        $tags = $this->transformTags($request->tags);

        $article->tags()->detach($tags);

        return $article->tags;
    }

    /**
     * Syncs tags with article
     */
    public function sync(Request $request, Article $article)
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'required|string|max:255'
        ]);

        $tags = $this->transformTags($request->tags);

        $article->tags()->sync($tags);

        return $article->tags;
    }

    /**
     * Tranforms tag strings to Tag model keys.
     *
     * @param array $tags
     * @return array
     */
    protected function transformTags(array $tags) {
        return collect($tags)->map(fn ($tag) => Tag::firstOrCreate(['tag' => $tag])->getKey());
    }
}
