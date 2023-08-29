@props(['article', 'comments', 'parent' => null])

@if ($comments->isNotEmpty())
<div class="col-12 mt-3">
    @foreach ($comments as $comment)
    <x-main.blog.comment :article="$article" :comment="$comment" :parent="$parent ?? null">
        @foreach ($comment->allChildren()->filter(fn($child) => $child->isApproved())->sortBy(fn ($comment) => $comment->post->created_at) as $child)
            <x-main.blog.comment :article="$article" :comment="$child" :parent="$parent ?? null" />
        @endforeach
    </x-main.blog.comment>
    @endforeach
</div>

@endif
