@props(['type', 'dismissable' => false])

@if (!$dismissable)
<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $slot }}
</div>
@else
<div {{ $attributes->merge(['class' => 'alert alert-'.$type.' alert-dismissible fade show']) }}>
    {{ $slot }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
</div>
@endif
