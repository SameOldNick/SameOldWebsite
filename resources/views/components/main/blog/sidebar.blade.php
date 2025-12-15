<aside class="blog-sidebar">
    <form action="{{ route('blog.search') }}" method="get" class="ibc-container mb-3">
        <input type="search" name="q" value="{{ request()->str('q') }}" class="form-control"
            placeholder="{{ __('Search') }}" aria-label="{{ __('Search') }}">
        <button class="ibc-button" type="submit">
            @svg('fas-magnifying-glass')
        </button>
    </form>

    <div class="mt-4 blog-sidebar-top-posts">
        <h5>{{ __('Most Recent') }}</h5>

        <div class="blog-sidebar-articles">
            @foreach ($mostRecent as $article)
                <a href="{{ $article->presenter()->publicUrl() }}" class="blog-sidebar-article py-3">
                    <div class="blog-sidebar-article-media">
                        @isset($article->mainImage)
                            <img src="{{ $article->mainImage->file->presenter()->publicUrl() }}"
                                alt="{{ $article->mainImage->alternativeText }}" class="img-cover img-thumbnail">
                        @else
                            <div class="blog-sidebar-article-media-placeholder img-thumbnail">
                                @svg('fas-image')
                            </div>
                        @endisset
                    </div>

                    <div class="blog-sidebar-article-content">
                        <h6 class="blog-sidebar-article-title">{{ $article->title }}</h6>
                        <ul class="mt-1 text-muted small list-inline">
                            <li class="list-inline-item" title="{{ $article->published_at }}">
                                @svg('fas-calendar-days')
                                {{ $article->published_at->toDateString() }}
                            </li>
                        </ul>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="mt-4 blog-sidebar-archives">
        <h5 class="mb-4">{{ __('Archives') }}</h5>

        <x-menu name="blog.sidebar.archives" renderer="listGroup" />
    </div>
</aside>
