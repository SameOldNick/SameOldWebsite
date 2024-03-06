<?php

namespace App\Http\Controllers\Api\Blog;

use App\Events\Articles\ArticleCreated;
use App\Events\Articles\ArticleDeleted;
use App\Events\Articles\ArticlePublished;
use App\Events\Articles\ArticleRestored;
use App\Events\Articles\ArticleRevisionUpdated;
use App\Events\Articles\ArticleScheduled;
use App\Events\Articles\ArticleUnpublished;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection as ArticleResourceCollection;
use App\Models\Article;
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
        $article = Article::createWithPost(function (Article $article) use ($request) {
            $article->fill([
                'title' => $request->title,
                'slug' => $request->slug,
            ]);

            $article->published_at = $request->date('published_at');
        });

        $revision = $article->revisions()->create([
            'content' => $request->string('revision.content'),
            'summary' => $request->string('revision.summary'),
        ]);

        $article->currentRevision()->associate($revision);

        // Don't use push because it will cause post is a circular dependency.
        $article->save();

        ArticleCreated::dispatch($article);
        ArticlePublished::dispatchIf($article->is_published, $article);
        ArticleScheduled::dispatchIf($article->is_scheduled, $article);

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

        if ($article->wasChanged('published_at')) {
            ArticlePublished::dispatchIf($article->is_published, $article);
            ArticleScheduled::dispatchIf($article->is_scheduled, $article);
            ArticleUnpublished::dispatchUnless($article->is_published, $article);
        }

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

        ArticleRevisionUpdated::dispatch($article);

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

        ArticleRestored::dispatch($article);

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

        ArticleDeleted::dispatch($article);

        return [
            'success' => __('Article ":title" was removed.', ['title' => $article->title]),
        ];
    }
}
