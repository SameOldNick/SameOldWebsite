<section id="projects" class="row mb-4 projects">
    <div class="col-12 mb-3">
        <h2 class="h3 fw-bold">Projects</h2>
    </div>
    <div class="col-12">
        <div class="row row-cols-1 row-cols-md-2 g-3">
            @foreach ($projects as $project)
                <div class="col">
                    <div class="card project">
                        <a href="{{ $project->url }}">
                            <div class="card-body">
                                <h3 class="h5 card-title">{{ $project->project }}</h3>
                                <p class="card-text">{{ $project->description }}</p>
                                <p class="card-text">
                                    @foreach ($project->tags as $tag)
                                        <span class="badge bg-primary">{{ $tag->tag }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
