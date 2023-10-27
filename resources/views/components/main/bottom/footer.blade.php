<footer class="py-5">
    <div class="container">
        <div class="row flex-wrap justify-content-between align-items-center">
            <p class="col-md-4 mb-0 fs-3 brand">
                <a href="{{ url('/') }}">SameOldNick.com</a>
            </p>

            <a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
                <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
            </a>

            <x-menu name="main" renderer="footer" />
        </div>
    </div>

</footer>
