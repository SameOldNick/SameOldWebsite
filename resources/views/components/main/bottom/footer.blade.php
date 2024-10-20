<footer class="bg-secondary py-5">
    <div class="container">
        <div class="row flex-wrap justify-content-between align-items-center">
            <p class="col-md-4 mb-0 brand text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ Vite::asset('resources/images/sameoldnick-text.png') }}" alt="{{ __('Same Old Nick') }}" class="img-fluid">
                </a>
            </p>

            <x-menu name="footer" renderer="footer" class="justify-content-center justify-content-md-end" />
        </div>
    </div>

</footer>
