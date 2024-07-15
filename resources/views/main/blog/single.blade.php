<x-main.layout class="page-blog-single">
    <x-slot:title>Blog</x-slot:title>

    <div class="container">
        <div class="row">
			<div class="col-md-8">
                <x-blog.article :article="$article" :revision="$revision ?? null" />

                <x-blog.comments :article="$article" />
			</div>

			<div class="col-md-4">
				<x-blog-sidebar />
			</div>
		</div>
    </div>

    @push('scripts')
    @endpush
</x-main.layout>
