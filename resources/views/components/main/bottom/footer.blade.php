<footer class="bg-secondary py-5">
    <div class="container">
        <div class="row flex-wrap justify-content-between align-items-center">
            <p class="col-md-4 mb-0 brand">
                <a href="{{ url('/') }}">
                    <img src="{{ Vite::asset('resources/images/sameoldnick-text.png') }}" alt="{{ __('Same Old Nick') }}" class="img-fluid">
                </a>
            </p>

            <x-menu name="footer" renderer="footer" />
        </div>
    </div>

</footer>
