<x-main.layout class="page-contact">
    <x-slot:title>{{ __('Contact Me') }}</x-slot:title>

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card card-container shadow-lg bg-light mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Contact Me') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="px-5 py-4">
                            @isset($success)
                                <div class="mb-4">
                                    <x-alert type="success">
                                        {{ $success }}
                                    </x-alert>
                                </div>
                            @endisset

                            @isset($error)
                                <div class="mb-4">
                                    <x-alert type="danger">
                                        {{ $error }}
                                    </x-alert>
                                </div>
                            @endisset

                            @if ($errors->any())
                                <div class="mb-4">
                                    <x-alerts type="danger" :messages="$errors->all()"></x-alerts>
                                </div>
                            @endif

                            <form method="POST" id="contact" name="contact" action="{{ route('contact') }}">
                                @csrf

                                @if ($settings['require_recaptcha'])
                                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                @endif

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="name" class="form-label">{{ __('Name') }}</label>
                                        <input name="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" id="name"
                                            value="{{ old('name', optional(Auth::user())->name) }}" required
                                            autocomplete="name">

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="email" class="form-label">{{ __('E-mail Address') }}</label>
                                        <input name="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            value="{{ old('email', optional(Auth::user())->email) }}" required
                                            autocomplete="email">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="message" class="form-label">{{ __('Message') }}</label>
                                        <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="5">{{ old('message') }}</textarea>

                                        @error('message')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center flex-column">
                                    <div class="mx-auto mt-2 mb-4">
                                        <button class="btn btn-primary text-center fs-5" type="submit">
                                            {{ __('Submit') }}
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        @if ($settings['require_recaptcha'])
            <script>
                function onToken(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                }
            </script>

            <x-captcha driver="recaptcha" js-callback="onToken" js-auto-call />
        @endif
    @endpush
</x-main.layout>
