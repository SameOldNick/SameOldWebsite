@props(['query', 'count'])

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
                        @foreach ($query->get('tags') as $tag)
                            <span class="badge rounded-pill bg-primary">#{{ $tag }}</span>
                        @endforeach

                        @foreach ($query->get('keywords') as $keyword)
                            <span class="badge rounded-pill bg-secondary">{{ $keyword }}</span>
                        @endforeach
                    </h3>
                    <p class="mb-0">{{ __(':count results', ['count' => $count]) }}</p>
                </div>
                <div>
                    <div class="btn-group" role="group" aria-label="Sort by buttons">
                        <a
                            href="{{ $sortByRelevanceLink }}"
                            @class(['btn', 'btn-primary' => $request->isSortBy('relevance'), 'btn-outline-primary' => !$request->isSortBy('relevance')])
                            role="button"
                        >
                            {{ __('Relevance') }}
                            @if ($request->isSortBy('relevance'))
                                @if ($request->isOrderAscending())
                                    &uarr;
                                @else
                                    &darr;
                                @endif
                            @endif
                        </a>
                        <a
                            href="{{ $sortByDateLink }}"
                            @class(['btn', 'btn-primary' => $request->isSortBy('date'), 'btn-outline-primary' => !$request->isSortBy('date')])
                            role="button"
                        >
                            {{ __('Date') }}
                            @if ($request->isSortBy('date'))
                                @if ($request->isOrderAscending())
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
