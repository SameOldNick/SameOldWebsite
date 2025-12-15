@props(['article'])

<article class="card blog-post">
    <a href="{{ $url }}">
        <div class="card-body">
            <h3 class="blog-post-title h5 card-title">{{ $article->title }}</h3>
            <ul class="blog-post-metadata">
                <li title="{{ $article->published_at }}">
                    @svg('fas-calendar')
                    Posted {{ $article->published_at->longRelativeToNowDiffForHumans() }}
                </li>
                <li>
                    @svg('fas-comments')
                    {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $totalComments) }}
                </li>
            </ul>
            <p class="blog-post-summary card-text">
                {{ Str::stripTags(Str::markdown($article->revision->summary)) }}
                <span class="blog-post-read-more">{{ __('Continue reading') }} &rarr;</span>
            </p>
        </div>
    </a>
</article>
