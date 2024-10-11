@props(['articles'])

@if ($articles->isNotEmpty())
<section id="blogPosts" class="row blog-posts">
    <div class="col-12 mb-3">
        <h2 class="h3 fw-bold">{{ __('Recent Posts') }}</h2>
    </div>

    <div class="col-12">
        @foreach ($articles as $article)
            <x-homepage.article :article="$article" />
        @endforeach
    </div>
</section>
@endif
