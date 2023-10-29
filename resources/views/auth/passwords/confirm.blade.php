<x-main.layout class="container my-5 page-auth page-auth-password-confirm">
    <x-slot:title>{{ __('Confirm Password') }}</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card card-container shadow-lg mt-3">

                <div class="card-header card-header-banner">
                    <h2 class="text-center">{{ __('Confirm Password') }}</h2>
                </div>
                <div class="card-body">
                    <div class="px-5 py-4">
                        <p class="text-center">{{ __('Please confirm your password before continuing.') }}</p>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="input-group mb-3 has-validation">
                                <span class="input-group-text bg-secondary">
                                    <i class="fa-solid fa-key text-white"></i>
                                </span>
                                <input
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ __('Password') }}"
                                    required
                                    autocomplete="current-password"
                                    autofocus>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-center flex-column">
                                <div class="mx-auto mt-2 mb-4">
                                    <button class="btn btn-secondary text-center fs-5" type="submit">
                                        {{ __('Confirm Password') }}
                                    </button>
                                </div>

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
</x-main.layout>
