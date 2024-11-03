<x-main.layout class="page-connected-devices">
    <x-slot:title>{{ __('Connected Accounts') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <form action="{{ route('user.connected-accounts.destroy', ['provider' => $provider]) }}" method="post">
                    @csrf
                    @method('delete')

                    <div class="card card-container shadow-lg bg-light mt-3">

                        <div class="card-header card-header-banner">
                            <h2 class="text-center">Disconnect <span id="providerName">{{ $name }}</span>?</h2>
                        </div>
                        <div class="card-body text-center">
                            <p class="card-text">Are you sure you want to disconnect <span id="providerName">{{ $name }}</span> from your account?</p>
                            <button class="btn btn-danger me-2" type="submit">Yes, disconnect</button>
                            <a class="btn btn-secondary" href="{{ route('user.connected-accounts') }}">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-main.layout>
