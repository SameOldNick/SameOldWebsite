<x-main.layout class="page-auth page-auth-password-reset">
    <x-slot:title>{{ __('Reset Password') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Reset Password') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="p-4">
                            @if ($errors->any())
                                <div class="mb-4">
                                    <x-alerts type="danger" :messages="$errors->all(':message')"></x-alerts>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="input-group mb-3 has-validation">
                                    <span class="input-group-text bg-secondary">
                                        <i class="fa-solid fa-at text-white"></i>
                                    </span>
                                    <input
                                        name="email"
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="{{ __('Email Address') }}"
                                        value="{{ $email ?? old('email') }}"
                                        required
                                        autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="input-group mb-3 has-validation">
                                    <span class="input-group-text bg-secondary text-white">
                                        <i class="fa-solid fa-key"></i>
                                    </span>

                                    <div class="ibc-container showhide-password flex-grow-1">
                                        <input
                                            name="password"
                                            type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="{{ __('Password') }}"
                                            required
                                            autocomplete="new-password">
                                        <a href="#" class="ibc-button" role="button">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-center flex-column mt-2">
                                    <div class="mx-auto mb-3">
                                        <button class="btn btn-secondary text-center" type="submit">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>

                                    @if (Route::has('password.request'))
                                    <p class="text-center">
                                        <a href="{{ route('password.request') }}" class="text-secondary">{{ __('Didn\'t receive a code?') }}</a>
                                    </p>
                                    @endif

                                    @if (Route::has('login'))
                                    <p class="text-center">
                                        Know your password?
                                        <a href="{{ route('login') }}" class="text-secondary">{{ __('Login') }}</a>
                                    </p>
                                    @endif

                                    @if (Route::has('register'))
                                    <p class="text-center">
                                        {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" class="text-secondary">{{ __('Register') }}</a>
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
</x-main.layout>
