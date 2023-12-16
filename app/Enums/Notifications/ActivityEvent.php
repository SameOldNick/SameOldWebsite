<?php

namespace App\Enums\Notifications;

enum ActivityEvent: string
{
    case UserRegistered = 'user-registered';
    case CommentCreated = 'comment-created';
    case ArticleCreated = 'article-created';
    case ArticlePublished = 'article-published';
    case ArticleScheduled = 'article-scheduled';
    case ArticleUnpublished = 'article-unpublished';
    case ArticleDeleted = 'article-deleted';
}
