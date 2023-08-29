<li {{ $item->attributes('li') }}>
    <a {{ $item->attributes('a')->merge(['class' => 'dropdown-item' . ($active ? ' active' : '')]) }} aria-current="page" href="{{ $item->getResolver()->resolve() }}">{{ $item->getContent() }}</a>
</li>
