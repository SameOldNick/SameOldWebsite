<x-main.layout class="page-auth page-auth-verify">
    <x-slot:title>{{ __('Verify Your Email Address') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card card-container shadow-lg mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Verify Your Email Address') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="px-1 py-4">
                            @if (session('resent'))
                                <x-alert type="success">
                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                </x-alert>
                            @endif

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
