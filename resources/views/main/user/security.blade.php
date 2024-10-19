<x-main.layout class="page-user-security">
    <x-slot:title>{{ __('Security') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card card-container shadow-lg bg-light mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Multi-Factor Authentication') }}</h2>
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

                            @if ($configured)
                                <div class="row mb-3 text-center">
                                    <div class="col-12 fw-bold fs-5">
                                        <p>Status: <span class="badge bg-success">{{ __('Configured') }}</span></p>
                                    </div>
                                    <div class="col-12 fw-bold fs-6">
                                        <x-alert type="warning">{{ __('Warning: Disabling MFA reduces the security of your account. Only proceed if you are sure.') }}</x-alert>
                                        <p class="card-text">{{ __('To disable MFA, please provide verification:') }}</p>
                                    </div>
                                </div>

                                <form action="{{ route('user.security.mfa.disable') }}" method="post" class="row justify-content-center">
                                    @csrf

                                    <!-- Password Input -->
                                    <div class="col-8 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>

                                    <!-- Current OTP Input -->
                                    <div class="col-8 mb-3">
                                        <label for="currentOtp" class="form-label">Current OTP</label>
                                        <input type="number" class="form-control" id="currentOtp" name="current_otp" autocomplete="off" required>
                                    </div>

                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-danger">Disable MFA</button>
                                    </div>
                                </form>
                            @else
                                <p class="card-text fw-bold fs-5 text-center">Status: <span class="badge bg-danger">{{ __('Not Configured') }}</span></p>
                                <p class="card-text fw-bold fs-6 text-center">{{ __('It is recommended you use two-factor authentication to increase the security of your account.') }}</p>

                                <form action="{{ route('user.security.mfa.confirm-password') }}" method="post" class="row justify-content-center">
                                    @csrf

                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                                            <input name="password" type="password" class="form-control" placeholder="{{ __('Enter Your Current Password') }}" aria-label="{{ __('Enter Your Current Password') }}" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">{{ __('Configure MFA') }}</button>

                                    </div>
                                </form>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>
