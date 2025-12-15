@props(['article'])

<article class="blog-article card">
    <div class="card-body">
        <section class="blog-article-header">
            <h1 class="blog-article-title">{{ $article->title }}</h1>

            <ul class="blog-article-meta">
                @isset($article->published_at)
                    <li class="blog-article-meta-item" title="{{ $article->published_at }}">
                        @svg('fas-calendar-days')
                        {{ __('Posted :dateTime', ['dateTime' => $article->published_at->longRelativeToNowDiffForHumans()]) }}
                    </li>
                @else
                    <li class="blog-article-meta-item">
                        @svg('fas-circle-xmark')
                        {{ __('Not Published') }}
                    </li>
                    @endif

                    <li class="blog-article-meta-item">
                        <a href="#comments">
                            @svg('fas-comments')
                            {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $totalComments) }}
                        </a>
                    </li>

                    <li class="list-inline-item">
                        @svg('fas-user-pen')
                        {{ $article->post->person->user->getDisplayName() }}
                    </li>
                </ul>
            </section>

            @isset($article->mainImage)
                <figure class="blog-article-preview">
                    <img src="{{ $article->mainImage->file->presenter()->publicUrl() }}"
                        alt="{{ $article->mainImage->alternativeText }}">
                </figure>
            @endisset

            <section class="blog-article-content">
                {!! Str::markdown($revision->content) !!}
            </section>

            <section class="blog-article-tags">
                <p>
                    @foreach ($article->tags as $tag)
                        <a href="{{ $tag->createLink() }}"
                            class="btn btn-secondary rounded-pill btn-sm">#{{ $tag->tag }}</a>
                    @endforeach
                </p>
            </section>
        </div>
    </article>
