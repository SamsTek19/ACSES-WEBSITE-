@php($user = $user ?? null)

<x-mail::message>
# Confirm your ACSES Portal email

Hello {{ $user?->fullname ?? 'there' }},

Thanks for joining the ACSES Portal! Please verify your email address so we can activate your account and keep your profile secure.

<x-mail::button :url="$verificationUrl">
Verify email address
</x-mail::button>

If you did not create this account, please ignore this email or contact the ACSES administrators immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
