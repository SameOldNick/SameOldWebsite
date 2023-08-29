@props(['article', 'comment', 'parent' => null])

@can('view', $comment)
<article class="blog-article-comment" id="{{ sprintf('comment-%d', $comment->getKey()) }}">
    <section class="blog-article-comment-left">
        <img src="{{ $comment->post->user->getAvatarUrl() }}" alt="{{ __('Avatar for: :name', ['name' => $comment->post->user->name]) }}" class="blog-article-comment-avatar" />
    </section>
    <section class="blog-article-comment-right">
        <section class="blog-article-comment-header">
            <div class="blog-article-comment-header-left">
                <h5 class="blog-article-comment-name">
                    {{ $comment->post->user->getDisplayName() }}
                </h5>

                @if (is_null($comment->approved_at))
                    <small>{{ __('Awaiting Approval') }}</small>
                @endif
            </div>
            <div class="blog-article-comment-header-right">
                <small title="{{ $comment->post->created_at }}">{{ __('Posted :duration', ['duration' => $comment->post->created_at->longRelativeToNowDiffForHumans()]) }}</small>
            </div>
        </section>

        <section class="blog-article-comment-content">
            {!! Str::markdown($comment->comment) !!}
        </section>


        <section class="blog-article-comment-actions">
            @can('reply-to', $comment)
                @if(isset($parent) && $parent->is($comment))
                    <hr>
                    <div class="mb-3">
                        <x-main.blog.comment-form-reply :article="$article" :comment="$comment" :parent="$parent" />
                    </div>
                @else
                    <a href="{{ sprintf('%s#comment-%d', route('blog.single', ['article' => $article, 'parent_comment_id' => $comment->getKey()]), $comment->getKey()) }}">
                        <i class="fa-solid fa-reply"></i>
                        {{ __('Reply') }}
                    </a>
                @endisset
            @endif


            {{-- TODO: Add delete action --}}
        </section>

        {{ $slot }}
    </section>
</article>
@endcan
