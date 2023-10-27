<section id="blogPosts" class="row blog-posts">
    <div class="col-12 mb-3">
        <h2 class="h3 fw-bold">{{ __('Recent Posts') }}</h2>
    </div>

    <div class="col-12">
        @foreach ($articles as $article)
        <article class="card blog-post">
            <a href="{{ $article->public_url }}">
                <div class="card-body">
                    <h3 class="blog-post-title h5 card-title">{{ $article->title }}</h3>
                    <ul class="blog-post-metadata">
                        <li title="{{ $article->published_at }}">
                            <i class="fa-solid fa-calendar"></i>
                            Posted {{ $article->published_at->longRelativeToNowDiffForHumans() }}
                        </li>
                        <li>
                            <i class="fa-solid fa-comments"></i>
                            {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $article->comments()->approved()->count()) }}
                        </li>
                    </ul>
                    <p class="blog-post-summary card-text">
                        {{ Str::stripTags(Str::markdown($article->revision->summary)) }}
                        <span class="blog-post-read-more">{{ __('Continue reading') }} &rarr;</span>
                    </p>
                </div>
            </a>
        </article>
        @endforeach
    </div>
</section>
