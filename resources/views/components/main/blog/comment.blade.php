@props(['article', 'comment', 'parent', 'level' => 1])

@can('view', $comment)
<article class="blog-article-comment" id="{{ $comment->generateElementId() }}">
    <section class="blog-article-comment-left">
        <img src="{{ $comment->avatar_url }}" alt="{{ __('Avatar for: :name', ['name' => $comment->commenter['display_name']]) }}" class="blog-article-comment-avatar" />
    </section>
    <section class="blog-article-comment-right">
        <section class="blog-article-comment-header">
            <div class="blog-article-comment-header-left">
                <h5 class="blog-article-comment-name">
                    {{ $comment->commenter['display_name'] }}
                </h5>

                @can('role-manage-comments')
                    <span class="badge text-bg-secondary">{{ Str::of($comment->status)->headline() }}</span>
                @else
                    @if ($comment->status !== 'approved')
                        <small>{{ Str::of($comment->status)->headline() }}</small>
                    @endif
                @endcan

            </div>
            <div class="blog-article-comment-header-right">
                <small title="{{ $comment->post->created_at }}">{{ __('Posted :duration', ['duration' => $comment->post->created_at->longRelativeToNowDiffForHumans()]) }}</small>
            </div>
        </section>

        <section class="blog-article-comment-content">
            {!! Str::markdown($comment->comment) !!}
        </section>


        <section class="blog-article-comment-actions">
            @can('reply', $comment)
                @if($parent && $parent->is($comment))
                    <hr>
                    <div class="mb-3">
                        <x-blog.comment-form :article="$article" :parent="$parent" />
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

        @foreach ($children as $child)
            <x-blog.comment :article="$article" :comment="$child" :parent="$parent" :level="$level + 1" />
        @endforeach
    </section>
</article>

@endcan
