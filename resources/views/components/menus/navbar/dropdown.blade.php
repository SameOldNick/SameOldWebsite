<li {{ $dropdown->attributes('li')->merge(['class' => 'nav-item dropdown']) }}>
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
        data-bs-toggle="dropdown" aria-expanded="false">
        {{ $dropdown->getText() }}
    </a>
    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
        {!! $inner !!}
    </ul>
</li>
