<x-mail::message>
Thank you for your comment!
Please verify your comment by clicking the link below:

<x-mail::button :url="$link">
Verify Comment
</x-mail::button>

If you did not post a comment, please disregard this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
