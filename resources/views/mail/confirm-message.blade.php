<x-mail::message>
You must confirm your e-mail address to send a message.

<x-mail::button :url="$url">
Verify E-mail Address
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
