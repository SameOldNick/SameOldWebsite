<?php

namespace App\Http\Controllers\Main;

use App\Components\Moderator\ModerationService;
use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Commenter;
use App\Traits\Controllers\HasPage;
use Illuminate\Support\Facades\URL;

class BlogCommentController extends Controller
{
    use HasPage;

    public function __construct(
        protected readonly ModerationService $moderationService
    ) {}

    /**
     * Shows the comment (if user has access)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Article $article, Comment $comment)
    {
        $this->authorize('view', [Comment::class, $article]);

        return redirect()->away($comment->createPublicLink());
    }

    /**
     * Previews a comment for a blog article
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function preview(Article $article, Comment $comment)
    {
        return redirect()->away($comment->createPrivateUrl());
    }

    /**
     * Processes submitted comment
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function comment(SweetAlerts $swal, CommentRequest $request, Article $article)
    {
        return $this->handleCommentRequest($swal, $request, $article);
    }

    /**
     * Processes submitted reply comment
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reply(SweetAlerts $swal, CommentRequest $request, Article $article, Comment $parent)
    {
        abort_if(! $parent->article->is($article), 404);
        $this->authorize('reply', $parent);

        return $this->handleCommentRequest($swal, $request, $article, $parent);
    }

    /**
     * Verifies the comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(SweetAlerts $swal, Article $article, Comment $comment)
    {
        $this->authorize('view', [$article, $comment]);

        $comment->commenter->markEmailAsVerified();

        $swal->success(function (SweetAlertBuilder $builder) {
            $builder
                ->title('Success')
                ->text(trans('blog.comments.verified'));
        });

        return redirect()->route('blog.single', compact('article'));
    }

    /**
     * Sends verification email
     */
    protected function sendVerificationEmail(Comment $comment): void
    {
        $verificationLink = URL::signedRoute('blog.comment.verify', ['article' => $comment->article, 'comment' => $comment]);

        $comment->commenter->sendEmailVerificationNotification($verificationLink);
    }

    /**
     * Handles a post comment request
     *
     * @param SweetAlerts $swal
     * @param CommentRequest $request
     * @param Article $article
     * @param Comment|null $parent
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleCommentRequest(SweetAlerts $swal, CommentRequest $request, Article $article, ?Comment $parent = null) {
        $userAuthentication = $this->getSettings()->setting('user_authentication');

        // Process comment
        $comment = $this->processComment($request, $article, $parent);

        // Send notification email (if required)
        if ($request->isGuest() && $userAuthentication === 'guest_verified') {
            $this->sendVerificationEmail($comment);
        }

        // Dispatch events
        CommentCreated::dispatch($comment);

        // Alert user
        $swal->success(function (SweetAlertBuilder $builder) use ($comment) {
            $builder
                ->title('Success')
                // All the user needs to know is the comment is approved or pending.
                ->text($comment->status === CommentStatus::Approved->value ? trans('blog.comments.approved') : trans('blog.comments.pending'));
        });

        return redirect()->route('blog.single', compact('article'));
    }

    /**
     * Process comment creation
     *
     * @return Comment
     */
    protected function processComment(CommentRequest $request, Article $article, ?Comment $parent = null)
    {
        $userAuthentication = $this->getSettings()->setting('user_authentication');
        $commentModeration = $this->getSettings()->setting('comment_moderation');

        $comment = Comment::createWithPost(function (Comment $comment) use ($request, $article, $userAuthentication, $parent) {
            $comment->fill(['title' => $request->title, 'comment' => $request->comment]);

            $comment->article()->associate($article);

            if (! is_null($parent)) {
                $comment->parent()->associate($parent);
            }

            // Attach commenter if user is guest.
            if ($request->isGuest()) {
                $commenter = new Commenter([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                /**
                 * Set email has verified if 'guest_unverified' is set.
                 * This is so later if the setting is changed to 'guest_verified' the comments won't be hidden.
                 */
                $commenter->email_verified_at = $userAuthentication === 'guest_unverified' ? now() : null;

                $commenter->save();

                $comment->commenter()->associate($commenter);
            }
        });

        // Run comment through moderator
        $flagged = $commentModeration !== 'disabled' ? $this->moderationService->moderate($comment) : false;

        if (($commentModeration === 'auto' && ! $flagged) || $commentModeration === 'disabled')
            $comment->statuses()->create(['status' => CommentStatus::Approved]);

        // Update will only occur if comment is dirty (has changes).
        $comment->save();

        return $comment;
    }

    /**
     * Gets the key for the page.
     *
     * @return string
     */
    protected function getPageKey()
    {
        return 'blog';
    }
}
