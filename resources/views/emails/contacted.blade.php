<x-mail::message>
# You've Been Contacted

Time: {{ $dateTime }}
Name: {{ $name }}
E-mail address: {{ $email }}

Message:
{{ $message }}

IP address: {{ $ipAddress }}
User agent: {{ $userAgent }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
