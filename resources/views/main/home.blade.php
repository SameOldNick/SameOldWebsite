<x-main.layout class="page-home">
    <x-slot:title>Home</x-slot:title>

    <div class="container">
        <div class="row">
            <aside class="col-md-4 profile">
                <div class="profile-sticky">
                    <x-homepage.avatar />

                    <h1 class="h2 text-center">
                        {{ $settings->setting('name') }}
                    </h1>

                    <p class="text-center mb-0 fw-bold">
                        {{ $settings->setting('headline') }}
                    </p>
                    <p class="text-center mb-0">
                        {{ $settings->setting('location') }}
                    </p>

                    <x-homepage.social-media />

                    <nav class="navbar navbar-expand-md sections-submenu">
                        <div class="container-fluid">

                            <button class="navbar-toggler w-100" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSectionsSubmenu" aria-controls="navbarSectionsSubmenu"
                                aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                                <span class="sections-submenu-current">{{ __('Biography') }}</span>
                                @svg('fas-caret-down', 'ms-1')
                            </button>

                            <div class="collapse navbar-collapse" id="navbarSectionsSubmenu">
                                <ul class="nav nav-pills flex-column flex-fill">
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center active"
                                            href="#biography">{{ __('Biography') }}</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center" href="#skills">{{ __('Skills') }}</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center"
                                            href="#technologies">{{ __('Technologies') }}</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center" href="#projects">{{ __('Projects') }}</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center"
                                            href="#blogPosts">{{ __('Recent Posts') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>

                </div>
            </aside>

            <main class="col">
                <x-homepage.biography />

                <x-homepage.skills />

                <x-homepage.technologies />

                <x-homepage.projects />

                <x-homepage.articles />
            </main>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-main.layout>
