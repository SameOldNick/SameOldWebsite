<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-write-posts');
    }

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
            'tags.*' => 'required|string|max:255',
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
            'tags.*' => 'required|string|max:255',
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
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
        ]);

        $tags = $this->transformTags($request->collect('tags')->all());

        $article->tags()->sync($tags);

        return $article->tags;
    }

    /**
     * Tranforms tag strings to Tag model keys.
     *
     * @return list<int> Array of keys for Tag models
     */
    protected function transformTags(array $tags)
    {
        return Tag::createFromStrings($tags)->map(fn (Tag $tag) => $tag->getKey())->all();
    }
}
