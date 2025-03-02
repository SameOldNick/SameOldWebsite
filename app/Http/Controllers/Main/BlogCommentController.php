<?php

namespace App\Http\Controllers\Main;

use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Middleware\ErrorsToSweetAlert;
use App\Http\Requests\CommentRequest;
use App\Mail\CommentVerification;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Person;
use App\Models\Post;
use App\Traits\Controllers\HasPage;
use Illuminate\Support\Facades\URL;

class BlogCommentController extends Controller
{
    use HasPage;

    public function __construct()
    {
        $this->middleware([ErrorsToSweetAlert::class]);
    }

    /**
     * Shows the comment (if user has access)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Article $article, Comment $comment)
    {
        $this->authorize('view', [Comment::class, $article]);

        return redirect()->away($comment->presenter()->publicUrl());
    }

    /**
     * Previews a comment for a blog article
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function preview(Article $article, Comment $comment)
    {
        return redirect()->away($comment->presenter()->privateUrl());
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

        $oldStatus = CommentStatus::from($comment->status);
        $comment->post->person->markEmailAsVerified();

        $swal->success(function (SweetAlertBuilder $builder) {
            $builder
                ->title('Success')
                ->text(trans('blog.comments.verified'));
        });

        CommentStatusChanged::dispatch($comment, $oldStatus);

        return redirect()->route('blog.single', compact('article'));
    }

    /**
     * Sends verification email
     */
    protected function sendVerificationEmail(Comment $comment): void
    {
        $verificationLink = URL::signedRoute('blog.comment.verify', ['article' => $comment->article, 'comment' => $comment]);

        $comment->post->person->sendMailable((new CommentVerification)->with(['link' => $verificationLink]));
    }

    /**
     * Handles a post comment request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleCommentRequest(SweetAlerts $swal, CommentRequest $request, Article $article, ?Comment $parent = null)
    {
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

        $comment = Comment::createWithPost(
            function (Comment $comment) use ($request, $article, $parent) {
                $comment->fill(['title' => $request->title, 'comment' => $request->comment]);

                $comment->article()->associate($article);

                if (! is_null($parent)) {
                    $comment->parent()->associate($parent);
                }
            },
            function (Post $post) use ($request, $userAuthentication) {
                if ($request->isGuest()) {
                    $person = Person::guest($request->name, $request->email);

                    /**
                     * Set email has verified if 'guest_unverified' is set.
                     * This is so later if the setting is changed to 'guest_verified' the comments won't be hidden.
                     */
                    $person->email_verified_at = $userAuthentication === 'guest_unverified' ? now() : null;
                } else {
                    $person = Person::registered($request->user());
                }

                $person->save();

                $post->person()->associate($person);
            }
        );

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
