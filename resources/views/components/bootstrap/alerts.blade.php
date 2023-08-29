@props(['type', 'messages', 'dismissable' => false])

@foreach ($messages as $message)
    <x-alert :type="$type" :dismissable="$dismissable ?? false">{{ $message }}</x-alert>
@endforeach
