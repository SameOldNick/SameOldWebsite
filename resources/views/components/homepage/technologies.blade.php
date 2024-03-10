<section id="technologies" class="row mb-4 technologies">
    <div class="col-12 mb-3">
        <h2 class="h3 fw-bold">Technologies</h2>
    </div>

    <div class="showcase row-cols-3 justify-content-center">
        @foreach ($technologies as $technology)
            <div class="showcase-item">
                <div class="showcase-item-icon">
                    {{ svg($technology->icon) }}
                </div>
                <h4 class="showcase-item-text">{{ $technology->technology }}</h4>
            </div>
        @endforeach
    </div>

</section>
