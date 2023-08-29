<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    @isset($title)
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    @endisset

    {{ $slot }}
</div>
