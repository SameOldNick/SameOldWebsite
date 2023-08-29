@props(['header', 'body', 'footer'])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @isset($start)
    {{ $start }}
    @endisset

    @isset($header)
    <div {{ $header->attributes->merge(['class' => 'card-header']) }}>
        {{ $header }}
    </div>
    @endisset

    @isset($body)
    <div {{ $body->attributes->merge(['class' => 'card-body']) }}>
        {{ $body }}
    </div>
    @endisset

    {{ $slot }}

    @isset($footer)
    <div {{ $footer->attributes->merge(['class' => 'card-footer']) }}>
        {{ $footer }}
    </div>
    @endisset

    @isset($after)
    {{ $after }}
    @endisset
</div>
