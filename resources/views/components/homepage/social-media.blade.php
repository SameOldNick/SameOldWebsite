<section id="social-media" class="d-flex justify-content-center">
    <div class="mt-3 mb-2 social-media-links">
        @foreach ($links as $link)
            <a @class(['social-media-link', "social-media-link-{$link->platform}"]) href="{{ $link->link }}" target="_blank">
                {{ svg($link->icon) }}
            </a>
        @endforeach
    </div>
</section>
