<x-main.layout class="container my-5">
    <x-slot:title>{{ __('User Profile') }}</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card card-container shadow-lg bg-light mt-3">

                <div class="card-header card-header-banner">
                    <h2 class="text-center">{{ __('User Profile') }}</h2>
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

                        @if ($errors->any())
                            <div class="mb-4">
                                <x-alerts type="danger" :messages="$errors->all()"></x-alerts>
                            </div>
                        @endif

                        <form method="POST" name="user-profile" action="{{ route('user.profile') }}">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', Auth::user()->name) }}" autocomplete="name">

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                    <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="address1" class="form-label">{{ __('Address') }}</label>
                                    <input name="address1" type="text" class="form-control @error('address1') is-invalid @enderror" id="address1" value="{{ old('address1', Auth::user()->address1) }}" autocomplete="address">

                                    @error('address1')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="address2" class="form-label">{{ __('Apartment, suite, etc.') }}</label>
                                    <input name="address2" type="text" class="form-control @error('address2') is-invalid @enderror" id="address2" value="{{ old('address2', Auth::user()->address2) }}">

                                    @error('address2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="country" class="form-label">{{ __('Country') }}</label>

                                    <select name="country" id="country" class="form-control" title="{{ __('Select Country') }}" data-searchable-modal="true">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->code }}" @selected(old('country', Auth::user()->country_code) == $country->code)>{{ __($country->country) }} ({{ $country->code }})</option>
                                        @endforeach
                                    </select>

                                    @error('country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="state" class="form-label">{{ __('State/Province') }}</label>

                                    <div data-update-states="#country">
                                        @if(Auth::user()->isStateAssociated())
                                        <select name="state" id="state" class="form-control" title="{{ __('Select State/Province') }}" data-searchable-modal="true">
                                            @foreach (Auth::user()->country->states as $state)
                                                <option value="{{ $state->code }}" @selected(old('state', Auth::user()->stateReadable() ?? null) == $state->state)>{{ __($state->state) }} ({{ $state->code }})</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <input name="state" id="state" type="text" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', Auth::user()->stateReadable()) }}">
                                        @endif

                                    </div>

                                    @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input name="city" type="text" class="form-control @error('city') is-invalid @enderror" id="city" value="{{ old('city', Auth::user()->city) }}" autocomplete="city">

                                    @error('city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label">{{ __('Postal Code/ZIP Code') }}</label>
                                    <input name="postal_code" type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" value="{{ old('postal_code', Auth::user()->postal_code) }}">

                                    @error('postal_code')
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

    <x-country-states />
</x-main.layout>
