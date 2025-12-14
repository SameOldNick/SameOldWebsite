<x-main.layout class="page-auth page-auth-password-email">
    <x-slot:title>{{ __('Reset Password') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Reset Password') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="px-5 py-4">
                            @if (session('status'))
                                <div class="mb-4">
                                    <x-alert type="success">
                                        {{ session('status') }}
                                    </x-alert>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                                <div class="input-group mb-3 has-validation">
                                    <span class="input-group-text bg-secondary">
                                        @svg('fas-at', ['class' => 'text-white'])
                                    </span>
                                    <input name="email" type="text"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="{{ __('Email Address') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-center flex-column">
                                    <div class="mx-auto mt-2 mb-4">
                                        <button class="btn btn-secondary text-center" type="submit">
                                            {{ __('Send Password Reset Link') }}
                                        </button>
                                    </div>

                                    @if (Route::has('login'))
                                        <p class="text-center">
                                            Know your password?
                                            <a href="{{ route('login') }}"
                                                class="text-secondary">{{ __('Login') }}</a>
                                        </p>
                                    @endif

                                    @if (Route::has('register'))
                                        <p class="text-center">
                                            Don't have an account?
                                            <a href="{{ route('register') }}"
                                                class="text-secondary">{{ __('Register') }}</a>
                                        </p>
                                    @endif
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function onToken(token) {
                document.getElementById('g-recaptcha-response').value = token;
            }
        </script>

        <x-captcha driver="recaptcha" js-callback="onToken" js-auto-call />
    @endpush
</x-main.layout>
