<x-main.layout class="page-blog page-blog-main">
    <x-slot:title>Blog</x-slot:title>

    <div class="container">
        <div class="row">
            <div class="col-md-8 blog-articles">
                @if ($articles->isEmpty())
                    <h3>{{ __('No Articles') }}</h3>
                @else
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
