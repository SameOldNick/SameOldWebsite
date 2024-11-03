<x-main.layout class="page-connected-accounts">
    <x-slot:title>{{ __('Connected Accounts') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card card-container shadow-lg bg-light mt-3">

                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Connected Accounts') }}</h2>
                    </div>
                    <div class="card-body">
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

                        @empty($providers)
                            <x-alert type="info" class="text-center">
                                There are currently no configured OAuth providers.
                            </x-alert>
                        @else
                        
                            <p class="text-center mt-3">
                                Below is a list of third-party websites connected to your account. You can manage these connections here.
                            </p>
                            
                            <x-connected-accounts.provider-list :providers="$providers" />
                        @endempty
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-main.layout>
