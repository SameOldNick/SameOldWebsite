<x-main.layout class="page-auth page-auth-login">
    <x-slot:title>{{ __('Login') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner bg-secondary text-white">
                        <h1>{{ __('Login') }}</h1>
                    </div>
                    <div class="card-body">
                        <div class="p-4">
                            @if (session('success'))
                                <div class="mb-4">
                                    <x-alert type="success">
                                        {{ session('success') }}
                                    </x-alert>
                                </div>
                            @endif

                            @if (session('info'))
                                <div class="mb-4">
                                    <x-alert type="info">
                                        {{ session('info') }}
                                    </x-alert>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mb-4">
                                    <x-alerts type="danger" :messages="$errors->all()"></x-alerts>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <x-return-url-input :returnUrl="$returnUrl ?? old('return_url')" />

                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                                <div class="input-group mb-3 has-validation">
                                    <span class="input-group-text bg-secondary">
                                        <i class="fa-solid fa-at text-white"></i>
                                    </span>
                                    <input name="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required
                                        autocomplete="email" @if (!old('email')) autofocus @endif>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="input-group mb-3 has-validation">
                                    <span class="input-group-text bg-secondary">
                                        <i class="fa-solid fa-key text-white"></i>
                                    </span>
                                    <input name="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="{{ __('Password') }}" required autocomplete="current-password"
                                        @if (old('email')) autofocus @endif>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>



                                <div class="d-flex justify-content-center flex-column">
                                    <div class="mx-auto">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" @checked(old('remember'))>
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mx-auto">
                                        <button class="btn btn-secondary text-center mt-2" type="submit">
                                            {{ __('Login') }}
                                        </button>
                                    </div>

                                    <div class="login-or mt-3">
                                        <hr class="hr-or bg-dark">
                                        <span class="span-or bg-white">or</span>
                                    </div>

                                    <div class="text-center">
                                        @if (OAuth::provider('github')->isConfigured())
                                            <a class="btn-social btn-social-githubdark"
                                                href="{{ route('oauth.redirect', ['provider' => 'github']) }}">
                                                @svg('fab-github')
                                            </a>
                                        @endif

                                        @if (OAuth::provider('google')->isConfigured())
                                            <a class="btn-social btn-social-googleblue"
                                                href="{{ route('oauth.redirect', ['provider' => 'google']) }}">
                                                @svg('fab-google')
                                            </a>
                                        @endif

                                        @if (OAuth::provider('twitter')->isConfigured())
                                            <a class="btn-social btn-social-x-twitter"
                                                href="{{ route('oauth.redirect', ['provider' => 'twitter']) }}">
                                                @svg('fab-x-twitter')
                                            </a>
                                        @endif
                                    </div>

                                    @if (Route::has('register'))
                                        <p class="text-center">
                                            {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}"
                                                class="text-secondary">{{ __('Register') }}</a>
                                        </p>
                                    @endif

                                    @if (Route::has('password.request'))
                                        <p class="text-center">
                                            <a href="{{ route('password.request') }}" class="text-secondary">
                                                {{ __('Forgot your password?') }}
                                            </a>
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
