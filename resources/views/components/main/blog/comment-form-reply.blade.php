@props(['article', 'parent'])

<div>
    <form action="{{ route('blog.comment.reply-to', compact('article', 'parent')) }}" method="post" class="row">
        @csrf

        <div class="col-12">
            <p>{{ __('Replying to comment as:') }} <strong>{{ Auth::user()->getDisplayName() }}</strong></p>
        </div>

        <div class="col-12 mb-3">
            <label for="comment" class="form-label">
                {{ __('Comment') }} *
            </label>
            <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="5" required>{{ old('comment') }}</textarea>

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
