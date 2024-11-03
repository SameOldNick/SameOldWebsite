@push('head')
    <style>
        .provider-card {
            margin-bottom: 15px;
        }
        .provider-icon {
            width: 1.5rem; /* Adjust size as needed */
            margin-right: 10px;
        }
    </style>
@endpush

<div id="provider-list">
    @foreach ($providers as $provider)
        <x-connected-accounts.provider-item :providerName="$provider" />
    @endforeach
</div>