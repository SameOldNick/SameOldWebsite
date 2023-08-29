<a {{ $item->attributes()->merge(['class' => 'list-group-item list-group-item-action' . ($active ? ' active' : '')]) }} href="{{ $item->getResolver()->resolve() }}">{{ $item->getContent() }}</a>
