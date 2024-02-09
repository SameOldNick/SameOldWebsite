<x-main.layout class="page-user-tfa-finish">
    <x-slot:title>{{ __('Security') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card card-container shadow-lg bg-light mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('MFA Setup Confirmation') }}</h2>
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text">Congratulations! Multi-Factor Authentication (MFA) has been successfully set up for your account.</p>
                        <p class="card-text">You can now securely access your account using MFA.</p>
                        <a href="{{ route('user.profile') }}" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>
