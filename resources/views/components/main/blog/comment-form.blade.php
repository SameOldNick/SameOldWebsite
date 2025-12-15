@props(['article'])

@cannot('create', \App\Models\Comment::class)
    <div class="alert alert-info d-flex align-items-center" role="alert">
        <div class="flex-shrink-0 me-2" aria-label="Info:">
            @svg('fas-circle-info')
        </div>
        <div>
            You must <a href='{{ route('login', ['return_url' => url()->current()]) }}' class="text-dark">login</a> or <a
                href='{{ route('register', ['return_url' => url()->current()]) }}' class="text-dark">signup</a> to post or
            reply to a comment.
        </div>
    </div>
@else
    <input @class([
        'form-control',
        'collapsable-input',
        'collapsed' => empty($errors->all()),
    ]) value="{{ __('Have a comment?') }}" readonly id="uncollapseLeaveComment"
        data-bs-toggle="collapse" data-bs-target="#collapseLeaveComment">

    <div @class(['collapse', 'show' => !empty($errors->all())]) id="collapseLeaveComment">
        <h2>{{ __('Leave a Comment') }}</h2>

        <form action="{{ route('blog.comment', compact('article')) }}" method="post"
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

                    @error('name')
                        <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="email" class="form-label">
                        {{ __('E-mail Address') }} *
                    </label>
                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                        id="{{ $generateElementId('email') }}" name="email" value="{{ old('email') }}" required>

                    @error('email')
                        <div id="emailFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
@endcannot
