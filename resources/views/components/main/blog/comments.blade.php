@props(['article', 'comments', 'parent' => null])

<section class="blog-article-comments" id="comments">
    <div class="card bg-white">
        <div class="card-body">
            <!-- Comment form-->
            <div class="row">
                @if (!$parent())
                    <div class="col-12 mb-3">
                        <x-blog.comment-form :article="$article" />
                    </div>
                @endif

                @if ($comments->isNotEmpty())
                    <div class="col-12 mt-3">
                        @foreach ($comments as $comment)
                            <x-blog.comment :article="$article" :comment="$comment" :parent="$parent()" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
