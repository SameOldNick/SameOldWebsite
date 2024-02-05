<x-main.layout class="container my-5">
    <x-slot:title>{{ __('Security') }}</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card card-container shadow-lg bg-light mt-3">

                <div class="card-header card-header-banner">
                    <h2 class="text-center">{{ __('MFA Setup') }}</h2>
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

                        <div class="accordion mb-3" id="accordionPanelsAuthSetup">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsAuthSetup-qrCode" aria-expanded="true" aria-controls="panelsAuthSetup-qrCode">
                                        Option A (QR Code):
                                    </button>
                                </h2>
                                <div id="panelsAuthSetup-qrCode" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <p>Follow these steps to setup multi-factor authentication:</p>
                                        <ol>
                                            <li>Download and install a multi-factor authenticator app (i.e.: Google Authenticator) on your mobile device.</li>
                                            <li>Scan the following QR code with the app.</li>
                                            <li>Enter the current authentication code below.</li>
                                        </ol>

                                        <div class="text-center">
                                            {!! QrCode::size(200)->generate($url); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsAuthSetup-setupKey" aria-expanded="false" aria-controls="panelsAuthSetup-setupKey">
                                        Option B (Setup Key):
                                    </button>
                                </h2>
                                <div id="panelsAuthSetup-setupKey" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <h3>Option B (Setup Key):</h3>
                                        <p>1. Enter the following into your authenticator app:</p>

                                        <div class="mb-3">
                                            <label for="formGroupAccountName" class="form-label">Account Name</label>
                                            <input type="text" readonly class="form-control" id="formGroupAccountName" value="{{ $accountName }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="formGroupSecret" class="form-label">Secret Key</label>
                                            <input type="text" readonly class="form-control" id="formGroupSecret" value="{{ $secret }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="formGroupKeyType" class="form-label">Type of Key</label>
                                            <input type="text" readonly class="form-control" id="formGroupKeyType" value="Time based">
                                        </div>

                                        <p>2. Enter the current authentication code below.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ URL::temporarySignedRoute('user.security.mfa.confirm-mfa', now()->addMinutes(30)) }}" method="post" class="row justify-content-center mb-3">
                            @csrf
                            <div class="col-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                                    <input type="number" name="code" id="code" class="form-control" placeholder="{{ __('Enter Authenticator Code') }}" aria-label="{{ __('Enter Authenticator Code') }}">
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Submit</button>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>
