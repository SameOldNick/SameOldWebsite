<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection as ArticleResourceCollection;
use App\Models\Article;
use App\Models\Collections\ArticleCollection;
use App\Models\Revision;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => 'sometimes|in:unpublished,published,scheduled,removed,all',
        ]);

        $query = Article::query();

        $show = (string) $request->str('show', 'all');

        if ($show === 'unpublished') {
            $query = $query->whereNull('published_at');
        } elseif ($show === 'published') {
            $query = $query->where(function (Builder $query) {
                $query
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            });
        } elseif ($show === 'scheduled') {
            $query = $query->where(function (Builder $query) {
                $query
                    ->whereNotNull('published_at')
                    ->where('published_at', '>', now());
            });
        } elseif ($show === 'removed') {
            $query = $query->onlyTrashed();
        } elseif ($show === 'all') {
            $query = $query->withTrashed();
        }

        return new ArticleResourceCollection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $article = new Article([
            'title' => $request->title,
            'slug' => $request->slug,
        ]);

        $article->published_at = $request->date('published_at');

        $article->save();

        $revision = $article->revisions()->make([
            'content' => $request->string('revision.content'),
            'summary' => $request->string('revision.summary'),
        ]);

        $article->currentRevision()->associate($revision);

        $article->push();

        return $article;
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return $article->append('private_url');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        foreach (['title', 'slug'] as $key) {
            if ($request->filled($key)) {
                $article->setAttribute($key, $request->string($key));
            }
        }

        $article->published_at = $request->date('published_at');

        $article->save();

        return $article;
    }

    /**
     * Updates current revision for article
     *
     * @param Request $request
     * @param Article $article
     * @return mixed
     */
    public function revision(Request $request, Article $article)
    {
        $request->validate([
            'revision' => [
                'nullable',
                'uuid',
                Rule::exists(Revision::class, 'uuid')->where('article_id', $article->getKey()),
            ],
        ]);

        if (! is_null($request->revision)) {
            $revision = Revision::find($request->revision);
            $article->currentRevision()->associate($revision);
        } else {
            $article->currentRevision()->dissociate();
        }

        $article->save();

        return $article;
    }

    /**
     * Restores the specified article.
     *
     * @param Article $article
     * @return array
     */
    public function restore(Article $article)
    {
        $article->restore();

        return [
            'success' => __('Article ":title" was restored.', ['title' => $article->title]),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return [
            'success' => __('Article ":title" was removed.', ['title' => $article->title]),
        ];
    }
}
