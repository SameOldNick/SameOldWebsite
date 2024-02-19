<x-mail::message>
Thank you for your message!
To complete your contact form submission, please confirm your email address by clicking the link below:

<x-mail::button :url="$url">
Verify E-mail Address
</x-mail::button>

If you did not make a contact form submission, please disregard this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
