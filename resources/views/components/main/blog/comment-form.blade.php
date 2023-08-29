@props(['comment', 'article', 'expand' => false])

@php
    $showForm = $expand || $errors->count() > 0 || $comment;
@endphp

@cannot('create', \App\Models\Comment::class)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <div class="flex-shrink-0 me-2" aria-label="Info:">
        <i class="fa-solid fa-circle-info"></i>
    </div>
    <div>
        You must <a href='{{ route('login', ['return_url' => url()->current()]) }}' class="text-dark">login</a> or <a href='{{ route('register', ['return_url' => url()->current()]) }}' class="text-dark">signup</a> to post or reply to a comment.
    </div>
</div>
@else
<input
    @class(['form-control', 'collapsable-input', 'collapsed' => !$showForm])
    value="{{ __('Have a comment?') }}"
    readonly
    data-bs-toggle="collapse"
    data-bs-target="#collapseLeaveComment"
>

<div @class(['collapse', 'show' => $showForm]) id="collapseLeaveComment">
    <h2>{{ __('Leave a Comment') }}</h2>

    <form action="{{ route('blog.comment', compact('article')) }}" method="post" class="row">
        @csrf

        <div class="col-12">
            <p>{{ __('Commenting as:') }} <strong>{{ Auth::user()->getDisplayName() }}</strong></p>
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
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
        </div>
    </form>

</div>
@endcannot
