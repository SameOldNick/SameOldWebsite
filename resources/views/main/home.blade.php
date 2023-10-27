<x-main.layout class="my-5 page-home">
    <x-slot:title>Home</x-slot:title>

    <div class="container">
        <div class="row">
            <aside class="col-md-4 profile">
                <div class="profile-sticky">
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

                    <x-homepage.social-media />

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

            <main class="col">
                <x-homepage.biography />

                <x-homepage.skills />

                <x-homepage.technologies />


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
            </main>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-main.layout>
