<x-main.layout class="page-blog page-blog-search">
    <x-slot:title>{{ __('Results for: :query', ['query' => $request->q]) }}</x-slot:title>

    <div class="container">
        <div class="row">
			<div class="col-md-8 blog-articles">
                @if(!$request->filled('q') || $errors->has('q'))
                    <div class="mb-4">
                        <x-alert type="danger">
                            {{ __('The search query was not specified or is invalid.') }}
                        </x-alert>
                    </div>
                @else
                    <div class="card mb-3">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>
                                        {{ __('Results for: ') }}
                                        @foreach ($request->parsedSearchQuery()->get('tags') as $tag)
                                            <span class="badge rounded-pill bg-primary">#{{ $tag }}</span>
                                        @endforeach

                                        @foreach ($request->parsedSearchQuery()->get('keywords') as $keyword)
                                            <span class="badge rounded-pill bg-secondary">{{ $keyword }}</span>
                                        @endforeach
                                    </h3>
                                    <p class="mb-0">{{ __(':count results', ['count' => $articles->count()]) }}</p>
                                </div>
                                <div>
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <a
                                            href="{{ $request->fullUrlWithQuery(['sort' => 'relevance', 'order' => $sortBy === 'relevance' ? $order === 'asc' ? 'desc' : 'asc' : null]) }}"
                                            @class(['btn', 'btn-primary' => $sortBy === 'relevance', 'btn-outline-primary' => $sortBy !== 'relevance'])
                                            role="button"
                                        >
                                            {{ __('Relevance') }}
                                            @if ($sortBy === 'relevance')
                                                @if ($order === 'asc')
                                                    &uarr;
                                                @else
                                                    &darr;
                                                @endif
                                            @endif
                                        </a>
                                        <a
                                            href="{{ $request->fullUrlWithQuery(['sort' => 'date', 'order' => $sortBy === 'date' ? $order === 'asc' ? 'desc' : 'asc' : null]) }}"
                                            @class(['btn', 'btn-primary' => $sortBy === 'date', 'btn-outline-primary' => $sortBy !== 'date'])
                                            role="button"
                                        >
                                            {{ __('Date') }}
                                            @if ($sortBy === 'date')
                                                @if ($order === 'asc')
                                                    &uarr;
                                                @else
                                                    &darr;
                                                @endif
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


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
