@props(['article', 'parent'])

<div>
    <form action="{{ route('blog.comment.reply', ['article' => $article, 'parent' => $parent]) }}" method="post"
        id="{{ $generateElementId('commentForm') }}" class="row">
        @csrf

        <input type="hidden" name="g-recaptcha-response" id="{{ $generateElementId('g-recaptcha-response') }}">

        <div class="col-12">
            @auth
                <p>{{ __('Commenting as:') }} <strong>{{ Auth::user()->getDisplayName() }}</strong></p>
            @else
                <label for="name" class="form-label">
                    {{ __('Name') }}
                </label>
                <input class="form-control @error('name') is-invalid @enderror" type="text"
                    id="{{ $generateElementId('name') }}" name="name" value="{{ old('name') }}">

                <label for="email" class="form-label">
                    {{ __('E-mail Address') }} *
                </label>
                <input class="form-control @error('email') is-invalid @enderror" type="email"
                    id="{{ $generateElementId('email') }}" name="email" value="{{ old('email') }}" required>
            @endauth
        </div>

        <div class="col-12 mb-3">
            <label for="comment" class="form-label">
                {{ __('Comment') }} *
            </label>
            <textarea class="form-control @error('comment') is-invalid @enderror" id="{{ $generateElementId('comment') }}"
                name="comment" rows="5" required>{{ $content }}</textarea>

            @error('comment')
                <div id="commentFeedback" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 text-end">
            <a href='{{ $article->presenter()->publicUrl() }}'
                class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
        </div>
    </form>


</div>

@if ($requireCaptcha())
    @push('scripts')
        <script>
            function onToken(token) {
                document.getElementById('{{ $generateElementId('g-recaptcha-response') }}').value = token;
            }
        </script>

        <x-captcha driver="recaptcha" js-callback="onToken" js-auto-call />
    @endpush
@endif
