<x-main.layout class="page-auth page-auth-verify">
    <x-slot:title>{{ __('Verify Your Email Address') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Verification Required') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="px-1 py-4">
                            <p class="text-center">
                                {{ __('Before proceeding, please check your email for a verification link.') }}
                            </p>

                            <div class="text-center">
                                {{ __('If you did not receive the email') }},
                                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>
