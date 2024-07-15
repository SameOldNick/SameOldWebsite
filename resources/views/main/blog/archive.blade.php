<x-main.layout class="page-blog page-blog-archive">
    <x-slot:title>{{ __('Articles From :yearMonth', ['yearMonth' => $dateTime->format('F Y')]) }}</x-slot:title>

    <div class="container">
        <div class="row">
			<div class="col-md-8 blog-articles">
                <div class="card mb-5">
                    <div class="card-body">

                        <h3>
                            @if($articles->isEmpty())
                            {{ __('No Articles From :yearMonth', ['yearMonth' => $dateTime->format('F Y')]) }}</h3>
                            @else
                            {{ __('Articles From :yearMonth', ['yearMonth' => $dateTime->format('F Y')]) }}
                            @endif
                        </h3>

                    </div>
                </div>

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
