@if ($paginator->hasPages())
    <nav class="blog-pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        @if ($paginator->onFirstPage())
            <a aria-disabled="true" aria-label="{{ trans('pagination.previous') }}"></a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">
                @svg('fas-arrow-left-long', 'me-2')
                {{ __('Newer Posts') }}
            </a>
        @endif

        @if (!$paginator->hasMorePages())
            <a aria-disabled="true" aria-label="{{ trans('pagination.next') }}"></a>
        @else
            <a href="{{ $paginator->nextPageUrl() }}">
                {{ __('Older Posts') }}
                @svg('fas-arrow-right-long', 'ms-2')
            </a>
        @endif
    </nav>
@endif
