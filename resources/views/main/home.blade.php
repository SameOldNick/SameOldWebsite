<x-main.layout class="my-5 page-home">
    <x-slot:title>Home</x-slot:title>

    <div class="container">
        <div class="row">
            <aside class="col-md-4 profile">
                <div class="profile-sticky">

                    <div class="d-flex justify-content-center">
                        <div class="">
                            <a class="btn-social btn-social-facebook" href="#">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>

                            <a class="btn-social btn-social-twitter" href="#">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                    <x-homepage.avatar />
                    <h1 class="h2 text-center">
                        {{-- Nick Hamnett --}}
                        {{ $settings->setting('name') }}
                    </h1>

                    <p class="text-center mb-0 fw-bold">
                        {{-- Instructor / Coder --}}
                        {{ $settings->setting('headline') }}
                    </p>
                    <p class="text-center mb-0">
                        {{-- Calgary, Canada --}}
                        {{ $settings->setting('location') }}
                    </p>


                    <nav class="navbar navbar-expand-md sections-submenu">
                        <div class="container-fluid">

                            <button class="navbar-toggler w-100" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSectionsSubmenu" aria-controls="navbarSectionsSubmenu" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="sections-submenu-current">Biography</span>
                                <i class="fa-solid fa-caret-down ms-1"></i>
                            </button>

                            <div class="collapse navbar-collapse" id="navbarSectionsSubmenu">
                                <ul class="nav nav-pills flex-column flex-fill">
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center active" href="#biography">Biography</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center" href="#skills">Skills</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center" href="#technologies">Technologies</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center" href="#projects">Projects</a>
                                    </li>
                                    <li class="nav-item flex-sm-fill">
                                        <a class="nav-link text-sm-center" href="#blogPosts">Recent Posts</a>
                                    </li>


                                </ul>
                            </div>
                        </div>
                    </nav>

                </div>
            </aside>

            <div id="sectionsScrollspyContainer" class="col" data-bs-spy="scroll" data-bs-target=".sections-submenu" data-bs-root-margin="0px 0px -70% 0px" data-bs-smooth-scroll="true" tabindex="0">
                <section id="biography" class="row biography">
                    <div class="col">
                        <h2 class="h3 fw-bold">Biography</h2>
                        <p>Hello 👋</p>

                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur quis consequat risus. Donec at dictum nisi, vel faucibus ex. Etiam ac erat ac urna finibus maximus sed a ligula. Ut pharetra scelerisque sapien, laoreet tempus massa blandit vel. Vestibulum lectus dui, bibendum vitae lorem eu, tincidunt maximus ex. Nunc interdum porta nisi molestie hendrerit. Praesent at ultricies lectus, id pharetra est. Quisque nec fringilla diam. Morbi et ligula sed tortor congue ornare quis vitae augue.</p>

                        <p>Duis ligula arcu, viverra rhoncus gravida vel, venenatis ut diam. Donec id condimentum nunc, at mattis nunc. Sed ultricies convallis ipsum nec posuere. Nulla non sem imperdiet nibh porttitor tincidunt. Nam velit dolor, sagittis quis congue non, mattis sit amet nibh. Vestibulum vel libero dolor. Phasellus a massa vitae urna dictum tincidunt. Sed vulputate lacus id eros consectetur, non dignissim metus viverra. In sollicitudin sem purus, vel vulputate mi ultrices sed. Pellentesque porttitor, nisi et elementum auctor, augue mauris semper leo, at blandit leo turpis sed ante. Nulla sem ante, sodales sed sapien at, lacinia interdum augue. In ullamcorper tristique tortor eget rutrum. Vivamus congue urna interdum est ullamcorper, vel ultricies velit viverra.</p>
                    </div>
                </section>

                <section id="skills" class="row mb-4 skills">
                    <div class="col-12 mb-3">
                        <h2 class="h3 fw-bold">Skills</h2>
                    </div>

                    <div class="showcase row-cols-3">
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-solid fa-code"></i>
                            </div>
                            <h4 class="showcase-item-text">Web Development</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <h4 class="showcase-item-text">Education</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-solid fa-mobile-screen"></i>
                            </div>
                            <h4 class="showcase-item-text">Mobile Development</h4>
                        </div>
                    </div>

                </section>
                <section id="technologies" class="row mb-4 technologies">
                    <div class="col-12 mb-3">
                        <h2 class="h3 fw-bold">Technologies</h2>
                    </div>

                    <div class="showcase row-cols-3">
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-brands fa-php"></i>
                            </div>
                            <h4 class="showcase-item-text">PHP</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-brands fa-js"></i>
                            </div>
                            <h4 class="showcase-item-text">JavaScript &amp; TypeScript</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-brands fa-java"></i>
                            </div>
                            <h4 class="showcase-item-text">Java</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-brands fa-react"></i>
                            </div>
                            <h4 class="showcase-item-text">React</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-brands fa-node"></i>
                            </div>
                            <h4 class="showcase-item-text">NodeJS</h4>
                        </div>
                        <div class="showcase-item">
                            <div class="showcase-item-icon">
                                <i class="fa-brands fa-laravel"></i>
                            </div>
                            <h4 class="showcase-item-text">Laravel</h4>
                        </div>
                    </div>

                </section>

                <section id="projects" class="row mb-4 projects">
                    <div class="col-12 mb-3">
                        <h2 class="h3 fw-bold">Projects</h2>
                    </div>
                    <div class="col-12">
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            @for ($i = 0; $i < 5; $i++)
                            <div class="col">
                                <div class="card project">
                                    <a href="#">
                                        <div class="card-body">
                                            <h3 class="h5 card-title">Electrux</h3>
                                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                            <p class="card-text">
                                                <span class="badge bg-primary">React</span>
                                                <span class="badge bg-primary">React</span>
                                                <span class="badge bg-primary">React</span>
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </section>

                <section id="blogPosts" class="row blog-posts">
                    <div class="col-12 mb-3">
                        <h2 class="h3 fw-bold">{{ __('Recent Posts') }}</h2>
                    </div>

                    <div class="col-12">
                        @foreach ($articles as $article)
                        <article class="card blog-post">
                            <a href="#">
                                <div class="card-body">
                                    <h3 class="blog-post-title h5 card-title">{{ $article->title }}</h3>
                                    <ul class="blog-post-metadata">
                                        <li title="{{ $article->published_at }}">
                                            <i class="fa-solid fa-calendar"></i>
                                            Posted {{ $article->published_at->longRelativeToNowDiffForHumans() }}
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-comments"></i>
                                            {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $article->comments()->approved()->count()) }}
                                        </li>
                                    </ul>
                                    <p class="blog-post-summary card-text">
                                        {{ Str::stripTags(Str::markdown($article->revision->summary)) }}
                                        <span class="blog-post-read-more">{{ __('Continue reading') }} &rarr;</span>
                                    </p>
                                </div>
                            </a>
                        </article>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-main.layout>
