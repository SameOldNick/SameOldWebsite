<li @class('nav-item')>
    <a @class(['nav-link', 'active' => $active]) aria-current="page" href="{{ $item->getResolver()->resolve() }}">
        @if ($item->hasProp('icon'))
            <span class="me-1">
                <i class="{{ $item->getProp('icon') }}"></i>
            </span>
        @endif

        {{ $item->getContent() }}
    </a>
</li>
