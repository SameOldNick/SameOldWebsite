<x-main.layout class="container my-5">
    <x-slot:title>{{ __('Security') }}</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card card-container shadow-lg bg-light mt-3">

                <div class="card-header card-header-banner">
                    <h2 class="text-center">{{ __('MFA Backup Codes') }}</h2>
                </div>
                <div class="card-body">
                    <div class="px-5 py-4">
                        @if ($errors->any())
                            <div class="mb-4">
                                <x-alerts type="danger" :messages="$errors->all()"></x-alerts>
                            </div>
                        @endif

                        <form action="{{ URL::temporarySignedRoute('user.security.mfa.confirm-backup', now()->addMinutes(30)) }}" method="post" class="row justify-content-center mb-3">
                            @csrf

                            <div class="mb-3">
                                <h5 class="mb-1">Your Backup Codes:</h5>
                                <ul class="list-group">
                                    @foreach ($codes as $code)
                                    <li class="list-group-item">{{ $code }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="stored" id="acknowledgeCheckbox">
                                <label class="form-check-label" for="acknowledgeCheckbox">
                                    I acknowledge that I have stored these backup codes securely.
                                </label>
                            </div>
                            <button class="btn btn-primary" id="submitBtn" disabled>
                                Continue
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Enable the button when the checkbox is checked
        document.getElementById('acknowledgeCheckbox').addEventListener('change', function() {
            document.getElementById('submitBtn').disabled = !this.checked;
        });
    </script>
</x-main.layout>
