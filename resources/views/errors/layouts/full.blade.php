<x-main.layout>
    <x-slot:title>@yield('title')</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">
                            <span>Error @yield('code') &mdash;</span>
                            <span>@yield('title')</span>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <p>@yield('message', __('An unknown error occurred.'))</p>

                            @yield('content')

                            <a href="javascript: window.history.go(-1)" class="btn btn-secondary mt-3">{{ __('Go Back') }}</a>
                            <a href="{{ route('home') }}" class="btn btn-secondary mt-3">{{ __('Back to Home') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-main.layout>



