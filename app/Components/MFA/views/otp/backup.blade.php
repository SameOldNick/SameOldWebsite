<x-main.layout class="page-auth page-auth-login">
    <x-slot:title>{{ __('Verification Needed') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner bg-secondary text-white">
                        <h1>{{ __('Verification Needed') }}</h1>
                    </div>
                    <div class="card-body">
                        <div class="p-md-4">
                            @if ($errors->any())
                                <div class="mb-4">
                                    <x-alerts type="danger" :messages="$errors->all()"></x-alerts>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('auth.mfa.backup.verify') }}">
                                @csrf

                                <x-return-url-input :returnUrl="redirect()->getIntendedUrl()" />

                                <x-alert type="warning">
                                    <strong>Warning: </strong>

                                    {{ __('You will need to reconfigure multi-factor authentication after verifying your backup code.') }}
                                </x-alert>

                                <p class="text-center">
                                    {{ __('Enter a backup code in order to continue.') }}
                                </p>

                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3 has-validation">
                                            <span class="input-group-text bg-secondary text-white">
                                                @svg('fas-key')
                                            </span>
                                            <input name="code" type="text"
                                                class="form-control @error('code') is-invalid @enderror"
                                                placeholder="{{ __('Backup Code') }}" required autofocus
                                                autocomplete="off">

                                            @error('code')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>



                                    </div>

                                </div>

                                <div class="d-flex justify-content-center flex-column">
                                    <div class="mx-auto">
                                        <button class="btn btn-primary text-center mt-2" type="submit">
                                            {{ __('Confirm') }}
                                        </button>
                                    </div>
                                    <div class="mx-auto mt-3">
                                        <p><a href="{{ route('auth.mfa') }}" class="link-secondary">Know the current
                                                code?</a></p>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>
