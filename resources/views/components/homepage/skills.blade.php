<section id="skills" class="row mb-4 skills">
    <div class="col-12 mb-3">
        <h2 class="h3 fw-bold">Skills</h2>
    </div>

    <div class="showcase row-cols-3">
        @foreach ($skills as $skill)
            <div class="showcase-item">
                <div class="showcase-item-icon">
                    {{ svg($skill->icon) }}
                </div>
                <h4 class="showcase-item-text">{{ $skill->skill }}</h4>
            </div>
        @endforeach
    </div>

</section>
