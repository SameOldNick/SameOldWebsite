<li @class('nav-item', 'dropdown')>
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        @if ($dropdown->hasProp('icon'))
            <span class="me-1">
                {{ svg($dropdown->getProp('icon')) }}
            </span>
        @endif

        {{ $dropdown->getText() }}
    </a>
    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
        {!! $inner !!}
    </ul>
</li>
