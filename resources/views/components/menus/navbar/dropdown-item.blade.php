<li>
    <a @class(['dropdown-item', 'active' => $active]) aria-current="page" href="{{ $item->getResolver()->resolve() }}">{{ $item->getContent() }}</a>
</li>
