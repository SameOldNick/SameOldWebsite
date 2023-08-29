<x-main.layout class="my-5 page-blog">
    <x-slot:title>Blog</x-slot:title>

    <div class="container">
        <div class="row">
			<div class="col-md-8 blog-articles">
                @if($articles->isEmpty())
                    <h3>{{ __('No Articles') }}</h3>
                @else

                    @foreach ($articles as $article)
                        <x-main.blog.article-preview :article="$article" />
                    @endforeach

                @endif

                {{ $articles->links('components.main.blog.pagination') }}
			</div>

			<div class="col-md-4">
				<x-blog-sidebar />
			</div>
		</div>
    </div>

    @push('scripts')
    @endpush
</x-main.layout>
