<?php

namespace App\Http\Controllers\Api\Blog;

use App\Events\Comments\CommentApproved;
use App\Events\Comments\CommentRemoved;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentCollection;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Traits\Controllers\HasPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    use HasPage;

    public function __construct()
    {
        $this->middleware('can:role-manage-comments');
    }

    /**
     * Displays blog settings.
     */
    public function show()
    {
        $keys = [
            'user_authentication',
            'comment_moderation',
            'use_captcha',
            'moderators',
        ];

        return $this->getPage()->metaData()->whereIn('key', $keys)->get();
    }

    /**
     * Updates blog settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'user_authentication' => 'required|string|in:guest_verified,guest_unverified,registered',
            'comment_moderation' => 'required|string|in:auto,manual,disabled',
            'use_captcha' => 'required|string|in:guest,all,disabled',

            'moderators' => 'required|array',
            'moderators.*' => Rule::in(['profanity', 'email', 'language', 'link']),
        ]);

        $page = $this->getPage();

        foreach ($validated as $key => $value) {
            $page->metaData()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return $this->pageUpdated()->getPage()->metaData;
    }

    /**
     * Gets the key for the page.
     *
     * @return string
     */
    protected function getPageKey() {
        return 'blog';
    }
}
