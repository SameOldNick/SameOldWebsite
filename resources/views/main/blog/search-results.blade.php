<x-main.layout class="page-blog page-blog-search">
    <x-slot:title>{{ __('Results for: :query', ['query' => $request->str('q')]) }}</x-slot:title>

    <div class="container">
        <div class="row">
			<div class="col-md-8 blog-articles">
                <x-blog.search-results-header :query="$query" :count="$articles->count()" />


                @if(!$articles->isEmpty())
                    @foreach ($articles as $article)
                        <x-blog.article :article="$article" preview />
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
