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
use App\Models\File;
use App\Models\Image;
use App\Models\Revision;
use App\Models\Tag;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function __construct()
    {
        // Apply middleware to check if the user has the 'role-write-posts' permission
        $this->middleware('can:role-write-posts');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate the 'show' parameter in the request
        $request->validate([
            'show' => 'sometimes|in:unpublished,published,scheduled,removed,all',
        ]);

        // Define an array of conditions based on the 'show' parameter
        $conditions = [
            'unpublished' => fn (Builder $query) => $query->whereNull('published_at'),
            'published' => fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '<=', now()),
            'scheduled' => fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '>', now()),
            'removed' => fn (Builder $query) => $query->onlyTrashed(),
            'all' => fn (Builder $query) => $query->withTrashed(),
        ];

        // Initialize the query for articles
        $query = Article::query();

        // Get the 'show' parameter from the request, default to 'all'
        $show = (string) $request->str('show', 'all');

        // Apply the appropriate condition to the query
        if (isset($conditions[$show])) {
            $conditions[$show]($query);
        }

        // Return the paginated collection of articles
        return new ArticleResourceCollection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Create a new article with the provided data
            $article = Article::createWithUser(function (Article $article) use ($request) {
                $article->fill([
                    'title' => $request->title,
                    'slug' => $request->slug,
                ]);

                $article->published_at = $request->date('published_at');
            });

            // Create a new revision for the article
            $revision = $article->revisions()->create([
                'content' => $request->string('revision.content'),
                'summary' => $request->filled('revision.summary') ? $request->string('revision.summary') : null,
            ]);

            // Associate the new revision with the article
            $article->currentRevision()->associate($revision);

            // Save the article without using push to avoid circular dependency
            $article->save();

            // Check if main image is supplied
            if ($request->has('main_image')) {
                // Create Image model
                $mainImage = new Image([
                    'description' => $request->has('main_image.description') ? $request->str('main_image.description') : null,
                ]);

                // Store image on disk
                $path = $request->file('main_image.image')->store('images');

                // Create File model
                $file = File::createFromFilePath($path, public: true);

                // Associate current user with file
                $file->user()->associate($request->user());

                // Save file and image models
                $mainImage->save();
                $mainImage->file()->save($file);

                // Attach image to article
                $article->images()->attach($mainImage);

                // Set as main image
                $article->mainImage()->associate($mainImage);
            }

            // Check for images
            if ($request->has('images')) {
                // Attach image UUIDs to article
                $images = $request->collect('images')->all();

                $article->images()->attach($images);
            }

            // Check if tags were supplied
            if ($request->has('tags')) {
                // Transform strings to Tag models
                $tags = Tag::createFromStrings($request->collect('tags'));

                // Attach tags to article
                $article->tags()->attach($tags->map(fn (Tag $tag) => $tag->getKey()));
            }

            $article->save();

            // Dispatch events related to article creation and publication status
            ArticleCreated::dispatch($article);
            ArticlePublished::dispatchIf($article->is_published, $article);
            ArticleScheduled::dispatchIf($article->is_scheduled, $article);

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to create article.'], 500);
        }

        return $article;
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return $article->appendPresenter();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Update the article's title and slug if they are provided in the request
            foreach (['title', 'slug'] as $key) {
                if ($request->filled($key)) {
                    $article->setAttribute($key, $request->string($key));
                }
            }

            if ($request->has('main_image')) {
                // Create Image model
                $mainImage = new Image([
                    'description' => $request->has('main_image.description') ? $request->str('main_image.description') : null,
                ]);

                // Store image on disk
                $path = $request->file('main_image.image')->store('images');

                // Create File model
                $file = File::createFromFilePath($path, public: true);

                // Associate current user with file
                $file->user()->associate($request->user());

                // Save file and image models
                $mainImage->save();
                $mainImage->file()->save($file);

                // Attach image to article
                $article->images()->attach($mainImage);

                // Set as main image
                $article->mainImage()->associate($mainImage);
            } elseif ($request->boolean('remove_main_image') && $article->mainImage) {
                $article->mainImage()->dissociate();

                $article->mainImage->delete();
            }

            // Check for images
            if ($request->has('images')) {
                // Attach image UUIDs to article
                $images = $request->collect('images')->all();

                $article->images()->attach($images);
            }

            // Check if tags were supplied
            if ($request->has('tags')) {
                // Transform strings to Tag models
                $tags = Tag::createFromStrings($request->collect('tags'));

                // Attach tags to article
                $article->tags()->sync($tags->map(fn (Tag $tag) => $tag->getKey()));
            }

            // Update the article's published date
            $article->published_at = $request->date('published_at');

            // Save the updated article
            $article->save();

            // Dispatch events based on changes to the article's publication status
            if ($article->wasChanged('published_at')) {
                ArticlePublished::dispatchIf($article->is_published, $article);
                ArticleScheduled::dispatchIf($article->is_scheduled, $article);
                ArticleUnpublished::dispatchUnless($article->is_published, $article);
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to create article.'], 500);
        }

        return $article;
    }

    /**
     * Updates current revision for article
     *
     * @return mixed
     */
    public function revision(Request $request, Article $article)
    {
        // Validate the 'revision' parameter in the request
        $request->validate([
            'revision' => [
                'nullable',
                'uuid',
                Rule::exists(Revision::class, 'uuid')->where('article_id', $article->getKey()),
            ],
        ]);

        // Associate or dissociate the revision with the article based on the request
        if (! is_null($request->revision)) {
            $revision = Revision::find($request->revision);
            $article->currentRevision()->associate($revision);
        } else {
            $article->currentRevision()->dissociate();
        }

        // Save the updated article
        $article->save();

        // Dispatch an event indicating the article's revision was updated
        ArticleRevisionUpdated::dispatch($article);

        return $article;
    }

    /**
     * Restores the specified article.
     */
    public function restore(Article $article)
    {
        // Check if the article is not deleted
        if (! $article->trashed()) {
            return response([
                'error' => __('Article ":title" is already restored.', ['title' => $article->title]),
            ], 409);
        }

        // Restore the deleted article
        $article->restore();

        // Dispatch an event indicating the article was restored
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
        // Delete the article
        $article->delete();

        // Dispatch an event indicating the article was deleted
        ArticleDeleted::dispatch($article);

        return [
            'success' => __('Article ":title" was removed.', ['title' => $article->title]),
        ];
    }
}
