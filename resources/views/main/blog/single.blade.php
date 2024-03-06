<x-main.layout class="page-blog-single">
    <x-slot:title>Blog</x-slot:title>

    <div class="container">
        <div class="row">
			<div class="col-md-8">
                <article class="blog-article card">
                    <div class="card-body">
                        <section class="blog-article-header">
                            <h1 class="blog-article-title">{{ $article->title }}</h1>

                            <ul class="blog-article-meta">
                                @isset($article->published_at)
                                <li class="blog-article-meta-item" title="{{ $article->published_at }}">
                                    <i class="fa-solid fa-calendar-days me-1"></i>
                                    {{ __('Posted :dateTime', ['dateTime' => $article->published_at->longRelativeToNowDiffForHumans()]) }}
                                </li>
                                @else
                                <li class="blog-article-meta-item">
                                    <i class="fa-regular fa-circle-xmark"></i>
                                    {{ __('Not Published') }}
                                </li>
                                @endif

                                <li class="blog-article-meta-item">
                                    <a href="#comments">
                                        <i class="fa-solid fa-comments me-1"></i>
                                        {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $article->comments()->approved()->count()) }}
                                    </a>
                                </li>

                                <li class="list-inline-item">
                                    <i class="fa-solid fa-user-pen me-1"></i>
                                    {{ $article->post->user->getDisplayName() }}
                                </li>
                            </ul>
                        </section>

                        @isset($article->mainImage)
                        <figure class="blog-article-preview">
                            <img src="{{ $article->mainImage->file->createPublicUrl() }}" alt="{{ $article->mainImage->alternativeText }}">
                        </figure>
                        @endisset

                        <section class="blog-article-content">
                            {!! Str::markdown($revision->content) !!}
                        </section>

                        <section class="blog-article-tags">
                            <p>
                                @foreach ($article->tags as $tag)
                                <a href="{{ $tag->createLink() }}" class="btn btn-secondary rounded-pill btn-sm">#{{ $tag->tag }}</a>
                                @endforeach
                            </p>
                        </section>
                    </div>
                </article>

                <section class="blog-article-comments" id="comments">
					<div class="card bg-white">
						<div class="card-body">
							<!-- Comment form-->
                            <div class="row">
                                @if (auth()->guest() || !isset($parentComment))
                                    <div class="col-12 mb-3">
                                        <x-main.blog.comment-form :article="$article" :comment="$comment" />
                                    </div>
                                @endif

                                <x-main.blog.comments :article="$article" :comments="$comments" :parent="$parentComment ?? null" />
                            </div>
						</div>
					</div>
				</section>
			</div>

			<div class="col-md-4">
				<x-blog-sidebar />
			</div>
		</div>
    </div>

    @push('scripts')
    @endpush
</x-main.layout>
