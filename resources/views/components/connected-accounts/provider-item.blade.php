@props(['icon', 'name'])

<div class="card provider-card">
    <div class="card-body d-flex justify-content-between align-items-center">
        @if ($icon)
        <div class="d-flex align-items-center">
            @svg($icon, 'provider-icon')
            <h5 class="card-title mb-0">{{ $name }}</h5>
        </div>
        @else
            <h5 class="card-title mb-0">{{ $name }}</h5>
        @endif

        @if(!$isConnected())
            <a class="btn btn-primary" href="{{ $connectUrl() }}">{{ __('Connect') }}</a>
        @else
            <a class="btn btn-danger" href="{{ $disconnectUrl() }}">{{ __('Disconnect') }}</a>

            <form action="{{ $disconnectUrl() }}" method="post" class="d-none">
                @csrf
                @method('delete')
            </form>
        @endif
    </div>
</div>