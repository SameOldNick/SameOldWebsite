@props(['article', 'parent'])

<div>
    <form action="{{ route('blog.comment.reply', ['article' => $article, 'parent' => $parent]) }}" method="post" class="row">
        @csrf

        <div class="col-12">
            @auth
                <p>{{ __('Commenting as:') }} <strong>{{ Auth::user()->getDisplayName() }}</strong></p>
            @else
                <label for="name" class="form-label">
                    {{ __('Name') }}
                </label>
                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name') }}">

                <label for="email" class="form-label">
                    {{ __('E-mail Address') }} *
                </label>
                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required>
            @endauth
        </div>

        <div class="col-12 mb-3">
            <label for="comment" class="form-label">
                {{ __('Comment') }} *
            </label>
            <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="5" required>{{ $content }}</textarea>

            @error('comment')
            <div id="commentFeedback" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 text-end">
            <a href='{{ $article->createPublicLink() }}' class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
        </div>
    </form>

</div>
