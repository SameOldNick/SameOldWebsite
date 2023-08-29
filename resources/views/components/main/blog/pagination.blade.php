@if ($paginator->hasPages())
<nav class="blog-pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    @if ($paginator->onFirstPage())
    <a aria-disabled="true" aria-label="{{ trans('pagination.previous') }}"></a>
    @else
    <a href="{{ $paginator->previousPageUrl() }}"><i class="fa-solid fa-arrow-left-long"></i> {{ __('Newer Posts') }}</a>
    @endif

    @if (!$paginator->hasMorePages())
    <a aria-disabled="true" aria-label="{{ trans('pagination.next') }}"></a>
    @else
    <a href="{{ $paginator->nextPageUrl() }}">{{ __('Older Posts') }} <i class="fa-solid fa-arrow-right-long"></i></a>
    @endif
</nav>
@endif
