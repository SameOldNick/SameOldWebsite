<x-main.layout class="container my-5">
    <x-slot:title>{{ __('Change Password') }}</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card card-container shadow-lg bg-light mt-3">

                <div class="card-header card-header-banner">
                    <h2 class="text-center">{{ __('Change Password') }}</h2>
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

                        @if(!Auth::user()->password)
                            <div class="mb-4">
                                <x-alert type="warning">
                                    <strong>{{ __('Important Notice:') }}</strong>
                                    {{ __('After setting a password, you will need to input it when utilizing OAuth (Open Authorization) for website access.') }}
                                </x-alert>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-4">
                                <x-alerts type="danger" :messages="$errors->all()"></x-alerts>
                            </div>
                        @endif

                        <form method="POST" name="user-change-password" action="{{ route('user.change-password') }}">
                            @csrf

                            @if(Auth::user()->password)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                                    <input name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" required autocomplete="password">

                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="new_password" class="form-label">{{ __('New Password') }}</label>

                                    <div class="ibc-container showhide-password flex-grow-1">
                                        <input name="new_password" id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" required>
                                        <a href="#" class="ibc-button" role="button">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>

                                    @error('new_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>

                            <div class="d-flex justify-content-center flex-column">
                                <div class="mx-auto mt-2 mb-4">
                                    <button class="btn btn-primary text-center fs-5" type="submit">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>
