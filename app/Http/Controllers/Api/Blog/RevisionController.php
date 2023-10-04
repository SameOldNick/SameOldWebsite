<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Revision;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RevisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Article $article)
    {
        return $article->revisions;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'parent' => [
                'nullable',
                'uuid',
                Rule::exists(Revision::class, 'uuid')->where('article_id', $article->getKey()),
            ],
        ]);

        $revision = new Revision([
            'content' => $validated['content'],
            'summary' => $validated['summary'],
        ]);

        if (isset($request->parent)) {
            $parentRevision = Revision::find($request->parent);
            $revision->parentRevision()->associate($parentRevision);
        } else {
            $latest = $article->revisions()->latest()->first();

            if (! is_null($latest)) {
                $revision->parentRevision()->associate($latest);
            }
        }

        $article->revisions()->save($revision);

        return $revision;
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article, Revision $revision)
    {
        return $revision;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article, Revision $revision)
    {
        $revision->delete();

        return [
            'success' => __('Revision ":revision" was removed.', ['revision' => $revision->getKey()]),
        ];
    }
}
