@props(['article'])

<a href="{{ $url }}" class="blog-article-link">
    <article class="blog-article card">
        <div class="card-body">
            <h3 class="blog-article-title">{{ $article->title }}</h3>
            <ul class="blog-article-meta list-inline">
                <li class="list-inline-item" title="{{ $article->published_at }}">
                    <i class="fa-solid fa-calendar-days me-1"></i>
                    {{ __('Posted :difference', ['difference' => $article->published_at->longRelativeToNowDiffForHumans()]) }}
                </li>
                <li class="list-inline-item">
                    <i class="fa-solid fa-comments me-1"></i>
                    {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $totalComments) }}
                </li>
                <li class="list-inline-item">
                    <i class="fa-solid fa-user-pen me-1"></i>
                    {{ $article->post->person->user->getDisplayName() }}
                </li>
            </ul>

            @isset($article->mainImage)
            <div class="blog-article-img">
                <div>
                    <img src="{{ $article->mainImage->file->presenter()->publicUrl() }}" alt="{{ $article->mainImage->alternativeText }}">
                </div>
            </div>
            @endisset

            <p class="blog-article-text">
                {{ Str::stripTags(Str::markdown($article->revision->summary)) }}
            </p>

            <div class="blog-article-read-more">
                <p>{{ __('Continue Reading') }} &rarr;</p>
            </div>
        </div>
    </article>
</a>
