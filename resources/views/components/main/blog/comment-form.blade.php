@props(['article'])

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
    @class(['form-control', 'collapsable-input', 'collapsed' => empty($errors->all())])
    value="{{ __('Have a comment?') }}"
    readonly
    id="uncollapseLeaveComment"
    data-bs-toggle="collapse"
    data-bs-target="#collapseLeaveComment"
>

<div @class(['collapse', 'show' => !empty($errors->all())]) id="collapseLeaveComment">
    <h2>{{ __('Leave a Comment') }}</h2>

    <form action="{{ route('blog.comment', compact('article')) }}" method="post" id="commentForm" class="row">
        @csrf

        <div class="col-12">
            @auth
                <p>{{ __('Commenting as:') }} <strong>{{ Auth::user()->getDisplayName() }}</strong></p>
            @else
                <label for="name" class="form-label">
                    {{ __('Name') }}
                </label>
                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name') }}">

                @error('name')
                <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="email" class="form-label">
                    {{ __('E-mail Address') }} *
                </label>
                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required>

                @error('email')
                <div id="emailFeedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
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
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
        </div>
    </form>

</div>
@endcannot
