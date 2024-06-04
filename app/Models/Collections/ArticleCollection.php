<?php

namespace App\Models\Collections;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Collection<int, Article>
 */
class ArticleCollection extends Collection
{
    /**
     * Gets articles with most comments
     *
     * @return static
     */
    public function popular()
    {
        return $this->sortBy(fn (Article $article) => $article->comments()->approved()->count());
    }

    /**
     * Gets articles grouped by date/time format
     */
    public function groupedByDateTime(string $format)
    {
        return $this->mapToGroups(fn (Article $article) => [$article->published_at->format($format) => $article]);
    }

    /**
     * Gets articles with any of tags
     *
     * @return static
     */
    public function withTags(array $tags)
    {
        return $this->weighted()->mapToWeight(function (Article $article) use ($tags) {
            return $article->tags->whereIn('slug', $tags)->count();
        });
    }

    /**
     * Gets articles that contains any of the keywords
     *
     * @return static
     */
    public function withKeywords(array $keywords, bool $ignoreCase = true)
    {
        $keywords = $ignoreCase ? Arr::map($keywords, fn ($keyword) => Str::lower($keyword)) : $keywords;

        return $this->weighted()->mapToWeight(function (Article $article) use ($keywords, $ignoreCase) {
            $articleContent = Str::of(Str::stripTags($article->revision->content));

            if ($ignoreCase) {
                $articleContent = $articleContent->lower();
            }

            $keywordCount = $this->countKeywordsInText($articleContent, $article->title, $keywords, $ignoreCase);

            $commentsCount = $article->comments->reduce(function ($carry, Comment $comment) use ($keywords, $ignoreCase) {
                return $carry + $this->countKeywordsInText(Str::of(Str::stripTags($comment->comment)), $comment->title, $keywords, $ignoreCase);
            }, 0);

            return $keywordCount + $commentsCount;
        });
    }

    /**
     * Count the occurrences of keywords in the given text and title
     *
     * @param  \Illuminate\Support\Stringable  $content
     * @param  string  $title
     * @param  array  $keywords
     * @param  bool  $ignoreCase
     * @return int
     */
    protected function countKeywordsInText($content, $title, $keywords, $ignoreCase)
    {
        return collect($keywords)->reduce(function ($carry, $keyword) use ($content, $title, $ignoreCase) {
            $keywordCount = $content->substrCount($keyword);
            if ($ignoreCase) {
                $keywordCount += Str::substrCount(Str::lower($title), $keyword);
            } else {
                $keywordCount += Str::substrCount($title, $keyword);
            }

            return $carry + $keywordCount;
        }, 0);
    }
}
